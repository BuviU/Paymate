/** ebox Lesson/Topic Video handler
 * Used when a Lesson or Topic contains an embed video ans allows
 */
if (typeof ebox_video_data !== "undefined") {
  if (typeof ebox_video_data.video_debug === "undefined") {
    ebox_video_data.video_debug = "1";
  }

  if (ebox_video_data.video_debug === "1") {
    console.log("DEBUG: ebox_video_data[%o]", ebox_video_data);
  }

  // Missing forEach on NodeList for IE11
  if (window.NodeList && !NodeList.prototype.forEach) {
    NodeList.prototype.forEach = Array.prototype.forEach;
  }

  /*
		var my_callback = function () {
			// Handler when the DOM is fully loaded
			console.log('in my_callback');
		};

		console.log('document.readyState[%o]', document.readyState);
		if ((document.readyState === "complete") ||(document.readyState !== "loading") && (!document.documentElement.doScroll)) {
			my_callback();
		} else {
			document.addEventListener('DOMContentLoaded', my_callback);
		}
	*/

  var ld_video_players = {};
  var ld_video_state = false;
  var ld_video_watch_interval = 2500;
  var ld_watchPlayers_interval_id = false;

  if (ebox_video_data.videos_found_provider == "youtube") {
    function onYouTubeIframeAPIReady() {
      if (
        document.querySelectorAll(
          '.ld-video[data-video-progression="true"][data-video-provider="' +
            ebox_video_data.videos_found_provider +
            '"] iframe, .ld-video[data-video-progression="true"][data-video-provider="' +
            ebox_video_data.videos_found_provider +
            '"] iframe'
        ).length
      ) {
        if (ebox_video_data.video_debug === "1") {
          console.log("YOUTUBE: calling ebox_disable_assets(true)");
        }
        ebox_disable_assets(true);
        ebox_watchPlayers();

        document
          .querySelectorAll(
            '.ld-video[data-video-progression="true"][data-video-provider="' +
              ebox_video_data.videos_found_provider +
              '"] iframe, .ld-video[data-video-progression="true"][data-video-provider="' +
              ebox_video_data.videos_found_provider +
              '"] video'
          )
          .forEach(function (element, index) {
            if (ebox_video_data.video_debug === "1") {
              console.log("YOUTUBE: index[%o] element[%o]", index, element);
            }

            var element_key = "ld-video-player-" + index;
            var element_id = element.getAttribute("id");
            if (
              typeof element_id === "undefined" ||
              element_id == null ||
              element_id == ""
            ) {
              element_id = element_key;
              element.setAttribute("id", element_id);
            }

            // We set our own attribute.
            element.setAttribute("data-ebox-video", element_key);

            ld_video_players[element_key] = {};
            ld_video_players[element_key].player_key = element_key;
            ld_video_players[element_key].player_type =
              ebox_video_data.videos_found_provider;
            ld_video_players[element_key].player_id = element_id;
            ld_video_players[element_key].player_wrapper =
              element.closest(".ld-video");
            if (
              typeof ld_video_players[element_key].player_wrapper !==
              "undefined"
            ) {
              ld_video_players[element_key].player_cookie_key =
                ld_video_players[element_key].player_wrapper.getAttribute(
                  "data-video-cookie-key"
                );
            } else {
              ld_video_players[element_key].player_cookie_key = "";
            }
            ld_video_players[element_key].player_cookie_values =
              ebox_Video_Progress_initSettings(
                ld_video_players[element_key]
              );
            ld_video_players[element_key].player = new YT.Player(element_id, {
              events: {
                onReady: ebox_YT_onPlayerReady,
                onStateChange: ebox_YT_onPlayerStateChange,
              },
            });
          });
      }
    }

    function ebox_YT_onPlayerReady(event) {
      if (ebox_video_data.video_debug === "1") {
        console.log(
          "YOUTUBE: in ebox_YT_onPlayerReady: event.target[%o]",
          event.target
        );
      }
      var ld_video_player = ebox_get_player_from_target(event.target);
      if (ebox_video_data.video_track_time === "1") {
        var user_video_time = ebox_Video_Progress_getSetting(
          ld_video_player,
          "video_time"
        );
        if (typeof user_video_time === "undefined") {
          user_video_time = 0;
        }

        if (ebox_video_data.video_debug === "1") {
          console.log(
            "YOUTUBE: in ebox_YT_onPlayerReady: start user_video_time: [%o]",
            user_video_time
          );
        }
        //event.target.seekTo( user_video_time );
      }

      if (ebox_video_data.videos_auto_start == true) {
        if (ebox_video_data.video_debug === "1") {
          console.log(
            "YOUTUBE: in ebox_YT_onPlayerReady: autostart enabled: calling playVideo()"
          );
        }
        event.target.mute();
        event.target.playVideo();
      } else {
        if (ebox_video_data.video_debug === "1") {
          console.log(
            "YOUTUBE: in ebox_YT_onPlayerReady: calling pauseVideo()"
          );
        }
        var videoID = ld_video_player.player.playerInfo.videoData.video_id;
        //event.target.pauseVideo();
        event.target.cueVideoById({
          videoId: videoID,
          startSeconds: user_video_time,
        });
      }
    }

    function ebox_YT_onPlayerStateChange(event) {
      var ld_video_player = ebox_get_player_from_target(event.target);

      var player_state = event.target.getPlayerState();
      if (player_state == YT.PlayerState.UNSTARTED) {
        // cspell:disable-line
        //if (ebox_video_data.video_debug === '1') {
        //	console.log('YOUTUBE: Video has not started[%o]', player_state);
        //}
      } else if (player_state == YT.PlayerState.ENDED) {
        if (ebox_video_data.video_debug === "1") {
          console.log("YOUTUBE: Video has ended[%o]", player_state);
        }

        if (ebox_video_data.video_debug === "1") {
          console.log("YOUTUBE: setting 'video_state' to 'complete'");
        }
        ebox_Video_Progress_setSetting(
          ld_video_player,
          "video_state",
          "complete"
        );

        // When the video ends we re-enable the form button.
        if (ebox_video_data.video_debug === "1") {
          console.log("YOUTUBE: calling ebox_disable_assets(false)");
        }
        ebox_disable_assets(false);

        // Stop watching players.
        ebox_watchPlayersEnd();
      } else if (player_state == YT.PlayerState.PLAYING) {
        if (ebox_video_data.video_debug === "1") {
          console.log("YOUTUBE: Video is playing");
        }
        ebox_Video_Progress_setSetting(
          ld_video_player,
          "video_state",
          "play"
        );
      } else if (player_state == YT.PlayerState.PAUSED) {
        if (ebox_video_data.video_debug === "1") {
          console.log("YOUTUBE: Video is paused");
        }
        ebox_Video_Progress_setSetting(
          ld_video_player,
          "video_state",
          "pause"
        );
      } else if (player_state == YT.PlayerState.BUFFERING) {
        //if (ebox_video_data.video_debug === '1') {
        //	console.log('YOUTUBE: Video is buffering');
        //}
      } else if (player_state == YT.PlayerState.CUED) {
        //if (ebox_video_data.video_debug === '1') {
        //	console.log('YOUTUBE: Video is queued');
        //}
      }
    }
  } else if (ebox_video_data.videos_found_provider == "vimeo") {
    jQuery(function () {
      if (ebox_video_data.video_debug === "1") {
        console.log("VIMEO: init");
      }

      if (typeof Vimeo === "undefined") {
        if (ebox_video_data.video_debug === "1") {
          console.log(
            "VIMEO: Error: Vimeo element not found. This is need for the video API."
          );
        }
      } else if (ebox_video_data.video_debug === "1") {
        console.log("VIMEO: Vimeo element found");
      }

      if (
        document.querySelectorAll(
          '.ld-video[data-video-progression="true"][data-video-provider="' +
            ebox_video_data.videos_found_provider +
            '"] iframe, .ld-video[data-video-progression="true"][data-video-provider="' +
            ebox_video_data.videos_found_provider +
            '"] video'
        ).length
      ) {
        if (ebox_video_data.video_debug === "1") {
          console.log("VIMEO: calling ebox_disable_assets(true)");
        }
        ebox_disable_assets(true);
        ebox_watchPlayers();

        document
          .querySelectorAll(
            '.ld-video[data-video-progression="true"][data-video-provider="' +
              ebox_video_data.videos_found_provider +
              '"] iframe, .ld-video[data-video-progression="true"][data-video-provider="' +
              ebox_video_data.videos_found_provider +
              '"] video'
          )
          .forEach(function (element, index) {
            var element_key = "ld-video-player-" + index;
            var element_id = element.getAttribute("id");
            if (typeof element_id === "undefined" || element_id == "") {
              element_id = element_key;
              element.setAttribute("id", element_id);
            }

            // We set our own attribute.
            element.setAttribute("data-ebox-video", element_key);

            ld_video_players[element_key] = {};
            ld_video_players[element_key].player_key = element_key;
            ld_video_players[element_key].player_type =
              ebox_video_data.videos_found_provider;
            ld_video_players[element_key].player_id = element_id;
            ld_video_players[element_key].player_wrapper =
              element.closest(".ld-video");
            if (
              typeof ld_video_players[element_key].player_wrapper !==
              "undefined"
            ) {
              ld_video_players[element_key].player_cookie_key =
                ld_video_players[element_key].player_wrapper.getAttribute(
                  "data-video-cookie-key"
                );
            } else {
              ld_video_players[element_key].player_cookie_key = "";
            }
            ld_video_players[element_key].player_cookie_values =
              ebox_Video_Progress_initSettings(
                ld_video_players[element_key]
              );
            ld_video_players[element_key].player = new Vimeo.Player(element);

            if (typeof ld_video_players[element_key] !== "undefined") {
              if (ebox_video_data.video_debug === "1") {
                console.log("VIMEO: player[%o]", ld_video_players[element_key]);
              }

              ld_video_players[element_key].player.ready().then(function () {
                if (ebox_video_data.video_debug === "1") {
                  console.log("VIMEO: ready video!");
                  console.log(
                    "VIMEO: element_key[%o] ld_video_players[%o]",
                    element_key,
                    ld_video_players
                  );
                }

                if (ebox_video_data.video_track_time === "1") {
                  var user_minutes = ebox_Video_Progress_getSetting(
                    ld_video_players[element_key],
                    "video_time"
                  );
                  if (typeof user_minutes === "undefined") {
                    user_minutes = 0;
                  }
                  if (ebox_video_data.video_debug === "1") {
                    console.log(
                      "VIMEO: start user_minutes: [%o]",
                      user_minutes
                    );
                  }

                  ld_video_players[element_key].player.setCurrentTime(
                    user_minutes
                  );
                }

                if (ebox_video_data.videos_auto_start == true) {
                  if (ebox_video_data.video_debug === "1") {
                    console.log("VIMEO: auto_start enabled.");
                    console.log("VIMEO: calling video play()");
                  }
                  //ld_video_players[element_key]['player'].mute();
                  ld_video_players[element_key].player.play();
                }
              });

              ld_video_players[element_key].player.on(
                "play",
                function (something) {
                  if (ebox_video_data.video_debug === "1") {
                    console.log("VIMEO: playing the video.");
                  }
                }
              );

              ld_video_players[element_key].player.on(
                "pause",
                function (something) {
                  if (ebox_video_data.video_debug === "1") {
                    console.log("VIMEO: paused the video.");
                  }
                }
              );

              ld_video_players[element_key].player.on(
                "ended",
                function (something) {
                  if (ebox_video_data.video_debug === "1") {
                    console.log("YOUTUBE: setting 'video_state' to 'complete'");
                  }
                  ebox_Video_Progress_setSetting(
                    ld_video_players[element_key],
                    "video_state",
                    "complete"
                  );

                  if (ebox_video_data.video_debug === "1") {
                    console.log(
                      "VIMEO: calling ebox_disable_assets(false)"
                    );
                  }
                  ebox_disable_assets(false);

                  // Stop watching players.
                  ebox_watchPlayersEnd();
                }
              );
            }
          });
      }
    });
  } else if (ebox_video_data.videos_found_provider == "wistia") {
    jQuery(function () {
      if (ebox_video_data.video_debug === "1") {
        console.log("WISTIA: init");
      }

      if (
        document.querySelectorAll(
          '.ld-video[data-video-progression="true"][data-video-provider="' +
            ebox_video_data.videos_found_provider +
            '"] iframe, .ld-video[data-video-progression="true"][data-video-provider="' +
            ebox_video_data.videos_found_provider +
            '"] video'
        ).length
      ) {
        if (ebox_video_data.video_debug === "1") {
          console.log("WISTIA: calling ebox_disable_assets(true)");
        }
        ebox_disable_assets(true);
        ebox_watchPlayers();

        window._wq = window._wq || [];

        document
          .querySelectorAll(
            '.ld-video[data-video-progression="true"][data-video-provider="' +
              ebox_video_data.videos_found_provider +
              '"] iframe, .ld-video[data-video-progression="true"][data-video-provider="' +
              ebox_video_data.videos_found_provider +
              '"] video'
          )
          .forEach(function (element, index) {
            var element_key = "ld-video-player-" + index;
            var element_id = element.getAttribute(
              "data-ebox-video-wistia-id"
            );
            if (typeof element_id === "undefined" || element_id == "") {
              element_id = element_key;
              element.setAttribute("id", element_id);
            }
            element.setAttribute("data-ebox-video", element_key);

            ld_video_players[element_key] = {};
            ld_video_players[element_key].player_key = element_key;
            ld_video_players[element_key].player_type =
              ebox_video_data.videos_found_provider;
            ld_video_players[element_key].player_id = element_id;
            ld_video_players[element_key].player_wrapper =
              element.closest(".ld-video");
            if (
              typeof ld_video_players[element_key].player_wrapper !==
              "undefined"
            ) {
              ld_video_players[element_key].player_cookie_key =
                ld_video_players[element_key].player_wrapper.getAttribute(
                  "data-video-cookie-key"
                );
            } else {
              ld_video_players[element_key].player_cookie_key = "";
            }
            ld_video_players[element_key].player_cookie_values =
              ebox_Video_Progress_initSettings(
                ld_video_players[element_key]
              );

            _wq.push({
              id: element_id,
              onReady: function (video) {
                ld_video_players[element_key].player = video;
                if (ebox_video_data.video_debug === "1") {
                  console.log("WISTIA: in onReady video[%o]", video);
                }

                ld_video_players[element_key].player.on("play", function () {
                  if (ebox_video_data.video_debug === "1") {
                    console.log("WISTIA: video started event");
                  }

                  if (ebox_video_data.video_debug === "1") {
                    console.log("WISTIA: setting 'video_state' to 'play'");
                  }
                  ebox_Video_Progress_setSetting(
                    ld_video_players[element_key],
                    "video_state",
                    "play"
                  );
                });

                ld_video_players[element_key].player.on("pause", function () {
                  if (ebox_video_data.video_debug === "1") {
                    console.log("WISTIA: video paused event");
                  }

                  if (ebox_video_data.video_debug === "1") {
                    console.log("WISTIA: setting 'video_state' to 'pause'");
                  }
                  ebox_Video_Progress_setSetting(
                    ld_video_players[element_key],
                    "video_state",
                    "pause"
                  );
                });

                ld_video_players[element_key].player.on("end", function () {
                  if (ebox_video_data.video_debug === "1") {
                    console.log("WISTIA: video ended event");
                  }

                  if (ebox_video_data.video_debug === "1") {
                    console.log("WISTIA: setting 'video_state' to 'complete'");
                  }
                  ebox_Video_Progress_setSetting(
                    ld_video_players[element_key],
                    "video_state",
                    "complete"
                  );

                  if (ebox_video_data.video_debug === "1") {
                    console.log(
                      "WISTIA: calling ebox_disable_assets(false)"
                    );
                  }
                  ebox_disable_assets(false);

                  // Stop watching players.
                  ebox_watchPlayersEnd();

                  return video.unbind;
                });

                if (ebox_video_data.video_track_time === "1") {
                  var user_minutes = ebox_Video_Progress_getSetting(
                    ld_video_players[element_key],
                    "video_time"
                  );
                  if (typeof user_minutes === "undefined") {
                    user_minutes = 0;
                  }

                  if (ebox_video_data.video_debug === "1") {
                    console.log(
                      "WISTIA: start user_minutes: [%o]",
                      user_minutes
                    );
                  }

                  // Set start position in video.
                  video.time(user_minutes);
                }

                if (ebox_video_data.videos_auto_start == true) {
                  if (ebox_video_data.video_debug === "1") {
                    console.log(
                      "WISTIA: auto-start enabled: calling video.play()"
                    );
                  }
                  video.play();

                  if (ebox_video_data.video_debug === "1") {
                    console.log("WISTIA: setting 'video_state' to 'play'");
                  }
                  ebox_Video_Progress_setSetting(
                    ld_video_players[element_key],
                    "video_state",
                    "play"
                  );
                }
              },
            });
          });
      }
    });
  } else if (ebox_video_data.videos_found_provider == "vooplayer") {
    jQuery(function () {
      if (
        document.querySelectorAll(
          '.ld-video[data-video-progression="true"][data-video-provider="' +
            ebox_video_data.videos_found_provider +
            '"] iframe, .ld-video[data-video-progression="true"][data-video-provider="' +
            ebox_video_data.videos_found_provider +
            '"] video'
        ).length
      ) {
        if (ebox_video_data.video_debug === "1") {
          console.log("VOOPLAYER: calling ebox_disable_assets(true)");
        }
        ebox_disable_assets(true);
        ebox_watchPlayers();

        document
          .querySelectorAll(
            '.ld-video[data-video-progression="true"][data-video-provider="' +
              ebox_video_data.videos_found_provider +
              '"] iframe, .ld-video[data-video-progression="true"][data-video-provider="' +
              ebox_video_data.videos_found_provider +
              '"] video'
          )
          .forEach(function (element, index) {
            var element_key = "ld-video-player-" + index;
            var element_id = element.getAttribute("id");
            if (typeof element_id === "undefined" || element_id == "") {
              element_id = element_key;
              element.setAttribute("id", element_id);
            }

            // We set our own attribute.
            element.setAttribute("data-ebox-video", element_key);

            ld_video_players[element_key] = {};
            ld_video_players[element_key].player_key = element_key;
            ld_video_players[element_key].player_type =
              ebox_video_data.videos_found_provider;
            ld_video_players[element_key].player_id = element_id;
            ld_video_players[element_key].player_wrapper =
              element.closest(".ld-video");
            if (
              typeof ld_video_players[element_key].player_wrapper !==
              "undefined"
            ) {
              ld_video_players[element_key].player_cookie_key =
                ld_video_players[element_key].player_wrapper.getAttribute(
                  "data-video-cookie-key"
                );
            } else {
              ld_video_players[element_key].player_cookie_key = "";
            }
            ld_video_players[element_key].player_cookie_values =
              ebox_Video_Progress_initSettings(
                ld_video_players[element_key]
              );
            ld_video_players[element_key].player = element;

            var vooPlayerID = element.getAttribute("data-playerid");
            if (ebox_video_data.video_debug === "1") {
              console.log("VOOPLAYER: vooPlayerID[%o]", vooPlayerID);
            }

            if (typeof vooPlayerID !== "undefined") {
              ld_video_players[element_key].vooPlayerID = vooPlayerID;

              document.addEventListener(
                "vooPlayerReady",
                LD_vooPlayerReady,
                false
              );
              function LD_vooPlayerReady(event) {
                if (ebox_video_data.video_debug === "1") {
                  console.log("VOOPLAYER: vooPlayerReady [%o]", event);
                }

                // See https://app.vooplayer.com/docs/api/#vooPlayerReady for event examples.

                if (
                  typeof event.detail.video !== "undefined" &&
                  event.detail.video.length > 0
                ) {
                  if (ebox_video_data.video_track_time === "1") {
                    let user_minutes = ebox_Video_Progress_getSetting(
                      ld_video_players[element_key],
                      "video_time"
                    );
                    if (typeof user_minutes === "undefined") {
                      user_minutes = 0;
                    }

                    if (ebox_video_data.video_debug === "1") {
                      console.log(
                        "VOOPLAYER: start user_minutes: [%o]",
                        user_minutes
                      );
                    }

                    let user_minutes_array = [];
                    user_minutes_array.push(user_minutes);

                    // Set start position in video.
                    vooAPI(
                      event.detail.video,
                      "currentTime",
                      user_minutes_array
                    );
                  }

                  if (ebox_video_data.videos_auto_start == true) {
                    if (ebox_video_data.video_debug === "1") {
                      console.log("VOOPLAYER: auto-start enabled");
                    }
                    vooAPI(event.detail.video, "play");
                  }

                  if (ebox_video_data.videos_show_controls == true) {
                    if (ebox_video_data.video_debug === "1") {
                      console.log("VOOPLAYER: videos_show_controls enabled");
                    }
                    vooAPI(event.detail.video, "showControls");
                  } else {
                    if (ebox_video_data.video_debug === "1") {
                      console.log("VOOPLAYER: videos_show_controls disabled");
                    }
                    vooAPI(event.detail.video, "hideControls");
                  }

                  vooAPI(
                    event.detail.video,
                    "onEnded",
                    null,
                    vooPlayer_onEnded
                  );
                  vooAPI(event.detail.video, "onPlay", null, vooPlayer_onPlay);
                  vooAPI(
                    event.detail.video,
                    "onPause",
                    null,
                    vooPlayer_onPause
                  );
                }
              }

              function vooPlayer_onEnded() {
                if (ebox_video_data.video_debug === "1") {
                  console.log("VOOPLAYER: setting 'video_state' to 'complete'");
                }
                ebox_Video_Progress_setSetting(
                  ld_video_players[element_key],
                  "video_state",
                  "complete"
                );

                if (ebox_video_data.video_debug === "1") {
                  console.log(
                    "VOOPLAYER: calling ebox_disable_assets(false)"
                  );
                }
                ebox_disable_assets(false);

                // Stop watching players.
                ebox_watchPlayersEnd();
              }

              function vooPlayer_onPlay() {
                if (ebox_video_data.video_debug === "1") {
                  console.log("VOOPLAYER: onPlay");
                }
                if (ebox_video_data.video_debug === "1") {
                  console.log("VOOPLAYER: setting 'video_state' to 'play'");
                }
                ebox_Video_Progress_setSetting(
                  ld_video_players[element_key],
                  "video_state",
                  "play"
                );
              }

              function vooPlayer_onPause() {
                if (ebox_video_data.video_debug === "1") {
                  console.log("VOOPLAYER: onPause");
                }
                if (ebox_video_data.video_debug === "1") {
                  console.log("VOOPLAYER: setting 'video_state' to 'pause'");
                }
                ebox_Video_Progress_setSetting(
                  ld_video_players[element_key],
                  "video_state",
                  "pause"
                );
              }
            }
          });
      }
    });
  } else if (ebox_video_data.videos_found_provider == "local") {
    jQuery(function () {
      if (ebox_video_data.video_debug === "1") {
        console.log("LOCAL: init");
      }

      if (
        document.querySelectorAll(
          '.ld-video[data-video-progression="true"][data-video-provider="' +
            ebox_video_data.videos_found_provider +
            '"] iframe, .ld-video[data-video-progression="true"][data-video-provider="' +
            ebox_video_data.videos_found_provider +
            '"] video'
        ).length
      ) {
        if (ebox_video_data.video_debug === "1") {
          console.log("LOCAL: calling ebox_disable_assets(true)");
        }
        ebox_disable_assets(true);
        ebox_watchPlayers();

        document
          .querySelectorAll(
            '.ld-video[data-video-progression="true"][data-video-provider="' +
              ebox_video_data.videos_found_provider +
              '"] iframe, .ld-video[data-video-progression="true"][data-video-provider="' +
              ebox_video_data.videos_found_provider +
              '"] video'
          )
          .forEach(function (element, index) {
            var element_key = "ld-video-player-" + index;
            var element_id = element.getAttribute("id");
            if (typeof element_id === "undefined" || element_id == "") {
              element_id = element_key;
              element.setAttribute("id", element_id);
            }

            // We set our own attribute.
            element.setAttribute("data-ebox-video", element_key);

            ld_video_players[element_key] = {};
            ld_video_players[element_key].player_key = element_key;
            ld_video_players[element_key].player_type =
              ebox_video_data.videos_found_provider;
            ld_video_players[element_key].player_id = element_id;
            ld_video_players[element_key].player_wrapper =
              element.closest(".ld-video");
            if (
              typeof ld_video_players[element_key].player_wrapper !==
              "undefined"
            ) {
              ld_video_players[element_key].player_cookie_key =
                ld_video_players[element_key].player_wrapper.getAttribute(
                  "data-video-cookie-key"
                );
            } else {
              ld_video_players[element_key].player_cookie_key = "";
            }
            ld_video_players[element_key].player_cookie_values =
              ebox_Video_Progress_initSettings(
                ld_video_players[element_key]
              );
            ld_video_players[element_key].player = element;

            if (ebox_video_data.video_track_time === "1") {
              var user_minutes = ebox_Video_Progress_getSetting(
                ld_video_players[element_key],
                "video_time"
              );
              if (typeof user_minutes === "undefined") {
                user_minutes = 0;
              }

              if (ebox_video_data.video_debug === "1") {
                console.log("LOCAL: start user_minutes: [%o]", user_minutes);
              }
              ld_video_players[element_key].player.currentTime = user_minutes;
            }

            if (ebox_video_data.videos_auto_start === "1") {
              if (ebox_video_data.video_debug === "1") {
                console.log("LOCAL: playing video");
              }
              ld_video_players[element_key].player.muted = true;
              ld_video_players[element_key].player.play();

              if (ebox_video_data.video_debug === "1") {
                console.log("LOCAL: setting 'video_state' to 'play'");
              }
              ebox_Video_Progress_setSetting(
                ld_video_players[element_key],
                "video_state",
                "play"
              );
            }

            ld_video_players[element_key].player.onended = function (e) {
              if (ebox_video_data.video_debug === "1") {
                console.log("LOCAL: video ended");
              }

              if (ebox_video_data.video_debug === "1") {
                console.log("LOCAL: setting 'video_state' to 'complete'");
              }
              ebox_Video_Progress_setSetting(
                ld_video_players[element_key],
                "video_state",
                "complete"
              );

              if (ebox_video_data.video_debug === "1") {
                console.log("LOCAL: calling ebox_disable_assets(false)");
              }
              ebox_disable_assets(false);

              // Stop watching players.
              ebox_watchPlayersEnd();
            };
          });
      }
    });
  }
}

/*
window.addEventListener('blur', function () {
	console.log('window blur');

	var ld_video_blur = new CustomEvent('ld_video_blur');

	// Dispatch the event
	window.dispatchEvent(ld_video_blur);

});

window.addEventListener('focus', function () {
	console.log('window onfocus');

	var ld_video_focus = new CustomEvent('ld_video_focus');

	// Dispatch the event
	window.dispatchEvent(ld_video_focus);
});
*/
function ebox_watchPlayers() {
  ld_watchPlayers_interval_id = setInterval(function () {
    if (document.hasFocus()) {
      ld_video_state = "focus";
    } else {
      ld_video_state = "blur";
    }

    if (Object.keys(ld_video_players).length !== 0) {
      for (var ld_video_key in ld_video_players) {
        if (ld_video_players.hasOwnProperty(ld_video_key)) {
          ld_video_player = ld_video_players[ld_video_key];

          if (typeof ld_video_player.player !== "undefined") {
            // Track User video time.
            //if (ebox_video_data.video_track_time === '1') {

            if (ld_video_player.player_type === "youtube") {
              var video_duration = ld_video_player.player.getDuration();
              if (typeof video_duration !== "undefined") {
                if (ebox_video_data.video_debug === "1") {
                  console.log("YOUTUBE: video duration: [%o]", video_duration);
                }
                ebox_Video_Progress_setSetting(
                  ld_video_player,
                  "video_duration",
                  video_duration
                );
              }

              var user_time = ld_video_player.player.getCurrentTime();
              if (ebox_video_data.video_debug === "1") {
                console.log("YOUTUBE: seconds[%o]", user_time);
              }
              ebox_Video_Progress_setSetting(
                ld_video_player,
                "video_time",
                user_time
              );
            } else if (ld_video_player.player_type === "vimeo") {
              ld_video_player.player
                .getPaused()
                .then(function (video_paused_state) {
                  if (ebox_video_data.video_debug === "1") {
                    console.log(
                      "VIMEO: video_paused_state[%o]",
                      video_paused_state
                    );
                  }
                  if (video_paused_state == true) {
                    ebox_Video_Progress_setSetting(
                      ld_video_player,
                      "video_state",
                      "pause"
                    );
                  } else {
                    ebox_Video_Progress_setSetting(
                      ld_video_player,
                      "video_state",
                      "play"
                    );
                  }
                });

              // Update Video duration.
              ld_video_player.player
                .getDuration()
                .then(function (video_duration) {
                  if (ebox_video_data.video_debug === "1") {
                    console.log("VIMEO: video_duration[%o]", video_duration);
                  }
                  ebox_Video_Progress_setSetting(
                    ld_video_player,
                    "video_duration",
                    video_duration
                  );
                });

              // Update user's Video progress minutes.
              ld_video_player.player.getCurrentTime().then(function (seconds) {
                if (ebox_video_data.video_debug === "1") {
                  console.log("VIMEO: seconds[%o]", seconds);
                }
                ebox_Video_Progress_setSetting(
                  ld_video_player,
                  "video_time",
                  seconds
                );
              });
            } else if (ld_video_player.player_type === "wistia") {
              var video_duration = ld_video_player.player.duration();
              if (typeof video_duration !== "undefined") {
                if (ebox_video_data.video_debug === "1") {
                  console.log("WISTIA: video duration: [%o]", video_duration);
                }
                ebox_Video_Progress_setSetting(
                  ld_video_player,
                  "video_duration",
                  video_duration
                );
              }

              var video_user_time = ld_video_player.player.time();
              if (ebox_video_data.video_debug === "1") {
                console.log("WISTIA: video user_time: [%o]", video_user_time);
              }
              ebox_Video_Progress_setSetting(
                ld_video_player,
                "video_time",
                video_user_time
              );
            } else if (ld_video_player.player_type === "vooplayer") {
              if (ld_video_player.vooPlayerID !== "undefined") {
                function getCurrentTimeCallback(event) {
                  if (ebox_video_data.video_debug === "1") {
                    console.log(
                      "VOOPLAYER: video user_time: [%o]",
                      event.returnValue
                    );
                  }
                  ebox_Video_Progress_setSetting(
                    ld_video_player,
                    "video_time",
                    event.returnValue
                  );
                }
                vooAPI(
                  ld_video_player.vooPlayerID,
                  "getCurrentTime",
                  [],
                  getCurrentTimeCallback
                );
              }
            } else if (ld_video_player.player_type === "local") {
              var has_ended = ld_video_player.player.ended;
              if (ebox_video_data.video_debug === "1") {
                console.log("LOCAL: has_ended: [%o]", has_ended);
              }

              if (ld_video_player.player.ended) {
                var video_state = "complete";
              } else if (ld_video_player.player.paused) {
                var video_state = "pause";
              } else {
                var video_state = "play";
              }

              if (ebox_video_data.video_debug === "1") {
                console.log("LOCAL: video_state: [%o]", video_state);
              }
              ebox_Video_Progress_setSetting(
                ld_video_player,
                "video_state",
                video_state
              );

              var video_duration = ld_video_player.player.duration;
              if (typeof video_duration !== "undefined") {
                if (ebox_video_data.video_debug === "1") {
                  console.log("LOCAL: video duration: [%o]", video_duration);
                }
                ebox_Video_Progress_setSetting(
                  ld_video_player,
                  "video_duration",
                  video_duration
                );
              }

              var user_video_time = ld_video_player.player.currentTime;
              if (ebox_video_data.video_debug === "1") {
                console.log("LOCAL: user_video_time: [%o]", user_video_time);
              }
              ebox_Video_Progress_setSetting(
                ld_video_player,
                "video_time",
                user_video_time
              );
            }
            //}

            //
            if (ebox_video_data.video_focus_pause === "1") {
              if (ld_video_player.player_type == "youtube") {
                if (ld_video_state === "focus") {
                  if (
                    ebox_video_data.videos_show_controls !== "1" &&
                    ebox_video_data.videos_auto_start === "1"
                  ) {
                    if (ebox_video_data.video_debug !== "1") {
                      console.log("YOUTUBE: Focus: calling playVideo()");
                    }
                    ld_video_player.player.playVideo();
                  }
                } else if (ld_video_state === "blur") {
                  if (ebox_video_data.video_debug === "1") {
                    console.log("YOUTUBE: Blur: calling pauseVideo()");
                  }
                  ld_video_player.player.pauseVideo();
                }
              } else if (ld_video_player.player_type === "vimeo") {
                if (ld_video_state === "focus") {
                  if (ebox_video_data.videos_show_controls != true) {
                    if (ebox_video_data.video_debug === "1") {
                      console.log("VIMEO: Focus: calling playVideo()");
                    }
                    ld_video_player.player.play();
                  }
                } else if (ld_video_state === "blur") {
                  if (ebox_video_data.video_debug === "1") {
                    console.log("VIMEO: Blur: calling pauseVideo()");
                  }
                  ld_video_player.player.pause();
                }
              } else if (ld_video_player.player_type === "wistia") {
                if (ld_video_state === "focus") {
                  if (ebox_video_data.videos_show_controls != true) {
                    //if (ebox_video_data.video_debug === '1') {
                    //	console.log('WISTIA: Blur: calling play()');
                    //}
                    //ld_video_player['player'].play();
                  }
                } else if (ld_video_state === "blur") {
                  if (ebox_video_data.video_debug === "1") {
                    console.log("WISTIA: Blur: calling pause()");
                  }
                  ld_video_player.player.pause();
                }
              } else if (ld_video_player.player_type === "vooplayer") {
                if (ld_video_player.vooPlayerID !== "undefined") {
                  if (ld_video_state === "focus") {
                    if (ebox_video_data.videos_show_controls != true) {
                      if (ebox_video_data.video_debug === "1") {
                        console.log("VOOPLAYER: Blur: calling play()");
                      }
                      vooAPI(ld_video_player.vooPlayerID, "play");
                    }
                  } else if (ld_video_state === "blur") {
                    if (ebox_video_data.video_debug === "1") {
                      console.log("VOOPLAYER: Blur: calling pause()");
                    }
                    vooAPI(ld_video_player.vooPlayerID, "pause");
                  }
                }
              } else if (ld_video_player.player_type === "local") {
                if (ld_video_state === "focus") {
                  if (ebox_video_data.videos_show_controls != true) {
                    if (ebox_video_data.video_debug === "1") {
                      console.log("LOCAL: Focus: calling playVideo()");
                    }
                    ld_video_player.player.play();
                  }
                } else if (ld_video_state === "blur") {
                  if (ebox_video_data.video_debug === "1") {
                    console.log("LOCAL: Blur: calling pauseVideo()");
                  }
                  ld_video_player.player.pause();
                }
              }
            }
          }
        }
      }
    }
  }, ld_video_watch_interval);
}

function ebox_watchPlayersEnd() {
  if (typeof ld_watchPlayers_interval_id !== "undefined") {
    if (ebox_video_data.video_debug === "1") {
      console.log(
        "Stop Watching Players: interval ID: [%o]",
        ld_watchPlayers_interval_id
      );
    }
    clearInterval(ld_watchPlayers_interval_id);
  }
}

function ebox_disable_assets(status) {
  if (
    jQuery("form.ebox-mark-complete input.ebox_mark_complete_button")
      .length
  ) {
    if (ebox_video_data.videos_hide_complete_button == true) {
      jQuery(
        "form.ebox-mark-complete input.ebox_mark_complete_button"
      ).hide();
    } else {
      jQuery(
        "form.ebox-mark-complete input.ebox_mark_complete_button"
      ).attr("disabled", status);
    }

    // If we enabled the button 'status' is false and auto-complete is true then submit the form.
    if (ebox_video_data.videos_auto_complete == true) {
      if (status == false) {
        var auto_complete_delay = parseInt(
          ebox_video_data.videos_auto_complete_delay
        );
        //console.log('auto_complete_delay[%o]', auto_complete_delay);

        if (auto_complete_delay > 0) {
          if (ebox_video_data.videos_auto_complete_delay_message != "") {
            var timer_html = jQuery(
              ebox_video_data.videos_auto_complete_delay_message
            ).insertAfter(
              "form.ebox-mark-complete input.ebox_mark_complete_button"
            );
          }

          var counter = auto_complete_delay;

          timer_id = setInterval(function () {
            counter--;
            if (counter < 1) {
              clearInterval(timer_id);

              //if ( typeof timer_html !== 'undefined' ) {
              //	jQuery('span', timer_html).html('XXX');
              //}
              jQuery("form.ebox-mark-complete")[0].submit();
            } else if (typeof timer_html !== "undefined") {
              jQuery("span", timer_html).html(counter);
            }
          }, 1000);
        } else {
          jQuery("form.ebox-mark-complete")[0].submit();
        }
      }
    }
  }

  if (ebox_video_data.videos_shown == "BEFORE") {
    if (status == true) {
      jQuery("#ebox_lesson_topics_list").hide();
      jQuery("#ebox_quizzes").hide();
    } else {
      jQuery("#ebox_lesson_topics_list").slideDown();
      jQuery("#ebox_quizzes").slideDown();
    }
  }

  jQuery(document).trigger("ebox_video_disable_assets", [status]);
}

/**
 * Function to GET the browser cookie by name.
 *
 * @since 3.2
 * @param string cookie_name Name of cookie. Required.
 * @param cookie_name
 * @param default_value
 * @param string cookie_name Name of cookie. Optional.
 * @return mixed cookie_values.
 */
function ebox_Video_Progress_getCookie(cookie_name, default_value) {
  if (cookie_name != "") {
    cookie_values = Cookies.get(cookie_name, {
      expires: ebox_video_data.video_track_expires,
      domain: ebox_video_data.video_track_domain,
    });
    if (typeof cookie_values === "undefined") {
      if (typeof default_value !== "undefined") {
        cookie_values = default_value;
      }
    } else if (cookie_values !== "") {
      cookie_values = JSON.parse(cookie_values);
    }
    return cookie_values;
  }
}

/**
 * Function to SET the browser cookie by name.
 *
 * @since 3.2
 * @param string cookie_name   Name of cookie.
 * @param cookie_name
 * @param cookie_values
 * @param Mixed  cookie_values Value(s) of cookie.
 */
function ebox_Video_Progress_setCookie(cookie_name, cookie_values) {
  if (cookie_name != "" && typeof cookie_values !== "undefined") {
    if (ebox_video_data.video_debug === "1") {
      //console.log('video_track_expires[%o]', Number(ebox_video_data.video_track_expires));
      console.log(
        "ebox_Video_Progress_setCookie: cookie_name[%o] cookie_values[%o]",
        cookie_name,
        cookie_values
      );
    }

    Cookies.set(cookie_name, JSON.stringify(cookie_values), {
      expires: Number(ebox_video_data.video_track_expires),
      domain: ebox_video_data.video_track_domain,
      path: ebox_video_data.video_track_path,
    });
  }
}

/**
 * Function to DELETE the browser cookie by name.
 *
 * @since 3.2
 * @param cookie_name
 * @param string cookie_name   Name of cookie.
 */
function ebox_Video_Progress_deleteCookie(cookie_name) {
  if (cookie_name != "") {
    Cookies.remove(cookie_name);
  }
}

/**
 * Function to initialize the ld_video_player settings.
 *
 * @since 3.2
 * @param ld_video_player
 * @param Object ld_player LD Player instance.
 */
function ebox_Video_Progress_initSettings(ld_video_player) {
  if (
    typeof ld_video_player !== "undefined" &&
    typeof ld_video_player.player_cookie_key !== "undefined" &&
    ld_video_player.player_cookie_key !== ""
  ) {
    cookie_values = ebox_Video_Progress_getCookie(
      ld_video_player.player_cookie_key,
      {}
    );

    var _changed = false;
    if (typeof cookie_values.video_time === "undefined") {
      cookie_values.video_time = 0;
      _changed = true;
    }

    if (typeof cookie_values.video_state === "undefined") {
      cookie_values.video_state = "";
      _changed = true;
    }

    if (_changed == true) {
      ebox_Video_Progress_setCookie(
        ld_video_player.player_cookie_key,
        cookie_values
      );
    }

    return cookie_values;
  }
}

function ebox_Video_Progress_setSetting(
  ld_video_player,
  player_setting_key,
  player_setting_value
) {
  if (
    typeof ld_video_player !== "undefined" &&
    typeof ld_video_player.player_cookie_key !== "undefined" &&
    ld_video_player.player_cookie_key !== ""
  ) {
    cookie_values = ebox_Video_Progress_getCookie(
      ld_video_player.player_cookie_key,
      {}
    );

    var _changed = false;

    if (
      player_setting_key == "video_time" ||
      player_setting_key == "video_duration"
    ) {
      player_setting_value = parseInt(player_setting_value);
    }

    if (
      player_setting_key === "video_state" &&
      cookie_values[player_setting_key] === "complete"
    ) {
      console.log("DEBUG: Video is already complete");
    } else if (cookie_values[player_setting_key] !== player_setting_value) {
      cookie_values[player_setting_key] = player_setting_value;
      _changed = true;
    }

    if (
      typeof cookie_values[player_setting_key] === "undefined" ||
      cookie_values[player_setting_key] !== player_setting_value
    ) {
      cookie_values[player_setting_key] = player_setting_value;
      _changed = true;
    }

    if (_changed == true) {
      ebox_Video_Progress_setCookie(
        ld_video_player.player_cookie_key,
        cookie_values
      );
    }
  }
}

function ebox_Video_Progress_getSetting(
  ld_video_player,
  player_setting_key
) {
  if (
    typeof ld_video_player !== "undefined" &&
    typeof ld_video_player.player_cookie_key !== "undefined" &&
    ld_video_player.player_cookie_key !== ""
  ) {
    cookie_values = ebox_Video_Progress_getCookie(
      ld_video_player.player_cookie_key,
      {}
    );

    if (typeof cookie_values[player_setting_key] !== "undefined") {
      return cookie_values[player_setting_key];
    }
  }
}

/**
 * Get the ld_video_player instance from the event_target.
 *
 * @since 3.2
 * @param event_target
 * @param Object event_target Object from the event.
 * @return Object ld_video_player instance or null.
 */
function ebox_get_player_from_target(event_target) {
  if (
    typeof event_target !== "undefined" &&
    Object.keys(ld_video_players).length !== 0
  ) {
    for (var ld_video_key in ld_video_players) {
      if (ld_video_players.hasOwnProperty(ld_video_key)) {
        ld_video_player = ld_video_players[ld_video_key];
        if (ld_video_player.player === event_target) {
          return ld_video_player;
        }
      }
    }
  }
}
