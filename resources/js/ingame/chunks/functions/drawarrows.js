function drawArrows(id) {
  var $techtree = $("div.graph[data-id='" + id + "']");
  var rowHeight = $techtree.find(".techImage").outerHeight(true);
  var overallWidth = 20;
  $techtree.find(".techWrapper.depth1").each(function () {
    overallWidth += $(this).outerWidth();
  });
  $techtree.css("width", overallWidth);
  var newTree = jsPlumb.getInstance();
  newTree.Defaults.Container = $techtree; // for round edges set corner radius below

  newTree.Defaults.Connector = [
    "Flowchart",
    {
      cornerRadius: 20,
    },
  ];
  newTree.Defaults.Endpoint = [
    "Rectangle",
    {
      cssClass: "endpoint",
      width: 1,
      height: 1,
    },
  ]; // default anchors. in most cases a bad idea, only as backup.
  // Continuous Anchors may produce partially overlapping connections

  newTree.Defaults.Anchors = ["ContinuousTop", "ContinuousBottom"]; // coordinates: an array containing the coordinates of each endpoint in pixels as [left,top]

  var coordinates = {}; // endpoints-array is set in singleTree.tpl.php. the values are techIds.

  $.each(endpoints, function () {
    var $elem = $techtree.find(".tech" + this.toString());
    newTree.addEndpoint($elem);

    // TODO OGAMEX: without this additional if check, the techtree throws errors.
    var elemOffset = $elem.find("a").offset();
    if (elemOffset) {
      var elemLeft = Math.floor(elemOffset.left);
      var elemTop = Math.floor(elemOffset.top);
      coordinates[this] = [elemLeft, elemTop];
    } else {
      coordinates[this] = [0, 0];
    }
  });
  var changedSomething;

  do {
    // for every connection:
    // check if the source is below the target on the screen. if not, move the source downwards one row
    changedSomething = false;
    $.each(connections, function () {
      var $source = $techtree.find(".tech" + this.source + " a");
      var $target = $techtree.find(".tech" + this.target + " a");

      // TODO OGAMEX: without this additional if check, the techtree throws errors.
      var sourceOffset = $source.offset();
      var targetOffset = $target.offset();

      if (sourceOffset == undefined || targetOffset == undefined) {
        return;
      }

      if (sourceOffset.top >= targetOffset.top - 10 && sourceOffset.top <= targetOffset.top + 10) {
        $source.parent().css("margin-top", parseInt($source.parent().css("margin-top").replace(/px/, "")) + rowHeight); // we just moved a tech downwards... we have to adjust all corresponding coordinates
        // the surrounding div with class depth* is the second parent

        $source
          .parent()
          .parent()
          .find("a[data-tech-id]")
          .each(function () {
            // TODO OGAMEX: without this additional if check, the techtree throws errors.
            if (coordinates[$(this).attr("data-tech-id")]) {
              coordinates[$(this).attr("data-tech-id")][1] += rowHeight; // 1 == top
              //console.log("RESET "+$(this).attr('data-tech-id') + " "+(coordinates[$(this).attr('data-tech-id')][1]));
            }
          });
        changedSomething = true;
      }
    });
  } while (changedSomething); // columns and rows: these 2 arrays will contain all different left- and top-values of the endpoint coordinates

  var columns = [];
  var rows = [];

  for (var elem in coordinates) {
    if (columns.indexOf(coordinates[elem][0]) == -1) {
      columns.push(coordinates[elem][0]);
    }

    if (rows.indexOf(coordinates[elem][1]) == -1) {
      rows.push(coordinates[elem][1]);
    }
  }

  columns.sort(function (a, b) {
    return a > b ? 1 : -1;
  });
  rows.sort(function (a, b) {
    return a > b ? 1 : -1;
  }); // now the 2 arrays contain a numeric sorted list
  // translated: an array containing the same keys as the coordinates-array, but the values aren't pixels anymore.
  // they are the numbers of the row/column containing that endpoint
  // with this information, it will be possible to test whether the path for a connection-line is free or not.

  var translated = {};

  for (var elem2 in coordinates) {
    translated[elem2] = {
      left: columns.indexOf(coordinates[elem2][0]),
      top: rows.indexOf(coordinates[elem2][1]),
    };
  } //console.dir(translated);
  // unfortunately, it matters in which order you put the connections into the drawing algorithm.
  // (because every line that is drawn blocks 1 anchor place at its 2 endpoints)
  // lines that will be drawn straight upwards have to be protected and drawn first..
  // -> sort by the minimum column distance

  connections.sort(function (a, b) {
    return Math.abs(translated[a.source]["left"] - translated[a.target]["left"]) <
      Math.abs(translated[b.source]["left"] - translated[b.target]["left"])
      ? -1
      : 1;
  }); // connection styling, see http://jsplumbtoolkit.com/doc/paint-styles

  var connectStyles = {
    hasRequirements: {
      strokeStyle: "#015100",
      lineWidth: 3,
    },
    hasNotRequirements: {
      strokeStyle: "#510009",
      lineWidth: 3,
    },
  }; // our anchors, format: [x-coordinate 0-1, y-coordinate 0-1, dx {-1;+1}, dy {-1;+1}
  // the first two parameters mark the point where the line starts at the endpoint
  // (change if you need, or add more entries),
  // the last two parameters give the initial direction of the line (do not change).
  // the anchors at the beginning of the anchor-arrays will be used first (try to keep it symmetrical)

  var leftAnchors = [
    [0, 0.5, -1, 0],
    [0, 0.3, -1, 0],
    [0, 0.7, -1, 0],
    [0, 0.9, -1, 0],
  ];
  var rightAnchors = [
    [1, 0.5, 1, 0],
    [1, 0.3, 1, 0],
    [1, 0.7, 1, 0],
    [1, 0.9, 1, 0],
  ];
  var bottomAnchors = [
    [0.5, 1, 0, 1],
    [0.3, 1, 0, 1],
    [0.7, 1, 0, 1],
    [0.9, 1, 0, 1],
  ];
  var topAnchors = [
    [0.5, 0, 0, -1],
    [0.3, 0, 0, -1],
    [0.7, 0, 0, -1],
    [0.9, 0, 0, -1],
  ]; // 0.9 is used in tech TECH_NETZTECHNIK. i guess that's the only one
  // we do not want to use the same anchor twice. so we have to remember which of the anchors was already used

  var alreadyUsedAnchors = {};

  function chooseAnchor(elemId, orientation, anchors, alreadyUsedAnchors) {
    if (!alreadyUsedAnchors[elemId]) {
      alreadyUsedAnchors[elemId] = {};
    }

    if (alreadyUsedAnchors[elemId][orientation] == undefined) {
      alreadyUsedAnchors[elemId][orientation] = 0;
    } // a stupid algorithm perhaps, but we only track the number of already used anchors.
    // one after we other is used. could be modified in the future; should be enough for now.

    ++alreadyUsedAnchors[elemId][orientation];
    return anchors[alreadyUsedAnchors[elemId][orientation] - 1];
  } // draw each connection

  $.each(connections, function () {
    var $source = $techtree.find(".tech" + this.source + " a");
    var $target = $techtree.find(".tech" + this.target + " a");
    var connectOptions = {
      source: $source,
      target: $target,
      overlays: [
        [
          "Arrow",
          {
            location: -5,
            paintStyle: connectStyles[this.paintStyle],
            width: 8,
            length: 8,
            foldback: 0.8,
          },
        ],
        [
          "Label",
          {
            label: this.label,
            cssClass: "label " + this.paintStyle,
            location: 0.85,
          },
        ],
      ],
      paintStyle: connectStyles[this.paintStyle],
      hoverPaintStyle: {
        strokeStyle: "rgb(255, 255, 0)",
      },
    }; // if you want to change options
    // consult the documentation at http://jsplumbtoolkit.com for details
    //var sourcePosition = $source.offset();
    //var targetPosition = $target.offset();
    //console.info("source:" + $source.attr('data-tech-name') + ", target: " + $target.attr('data-tech-name') + ". pos " + sourcePosition.left + "|" + sourcePosition.top + " vs pos " + targetPosition.left + "|" + targetPosition.top);
    //#############################################
    // search for the path that we line should take
    // it may not go through other endpoints/techs
    // it should use the shortest path and should not change directions too often (1-2)

    if (translated[this.target].left < translated[this.source].left) {
      // target is left of the source
      // TODO OGAMEX: uncommented this because it throws errors while drawing the techtree.
      // Even with identical HTML to the original game, it still doesn't work for certain
      // complicated tech trees.
      /*if (!lineInCoordinatesBlocked(translated, translated[this.source].left, translated[this.source].top, translated[this.source].left, translated[this.target].top) && !positionInCoordinatesBlocked(translated, translated[this.source].left, translated[this.target].top) && !lineInCoordinatesBlocked(translated, translated[this.source].left, translated[this.target].top, translated[this.target].left, translated[this.target].top)) {
        // vertical, horizontal
        connectOptions.anchors = [chooseAnchor(this.source, 'top', topAnchors, alreadyUsedAnchors), chooseAnchor(this.target, 'right', rightAnchors, alreadyUsedAnchors)]; //console.log("chose top right");
      } else {
        // horizontal, vertical
        connectOptions.anchors = [chooseAnchor(this.source, 'left', leftAnchors, alreadyUsedAnchors), chooseAnchor(this.target, 'bottom', bottomAnchors, alreadyUsedAnchors)];
        connectOptions.overlays[1][1] = readableVersionOfLabel(connectOptions.overlays[1][1], alreadyUsedAnchors[this.target].bottom); //console.log("chose left bottom");
      }*/
    } else if (translated[this.target].left > translated[this.source].left) {
      // target is right of the source
      // TODO OGAMEX: uncommented this because it throws errors while drawing the techtree.
      // Even with identical HTML to the original game, it still doesn't work for certain
      // complicated tech trees.
      /*if (!lineInCoordinatesBlocked(translated, translated[this.source].left, translated[this.source].top, translated[this.source].left, translated[this.target].top) && !positionInCoordinatesBlocked(translated, translated[this.source].left, translated[this.target].top) && !lineInCoordinatesBlocked(translated, translated[this.source].left, translated[this.target].top, translated[this.target].left, translated[this.target].top)) {
        // vertical, horizontal
        connectOptions.anchors = [chooseAnchor(this.source, 'top', topAnchors, alreadyUsedAnchors), chooseAnchor(this.target, 'left', leftAnchors, alreadyUsedAnchors)]; //console.log("chose top left");
      } else {
        // horizontal, vertical
        connectOptions.anchors = [chooseAnchor(this.source, 'right', rightAnchors, alreadyUsedAnchors), chooseAnchor(this.target, 'bottom', bottomAnchors, alreadyUsedAnchors)];
        connectOptions.overlays[1][1] = readableVersionOfLabel(connectOptions.overlays[1][1], alreadyUsedAnchors[this.target].bottom); //console.log("chose right bottom");
      }*/
    } else {
      // target is above the source
      if (
        translated[this.target].top < translated[this.source].top - 1 &&
        lineInCoordinatesBlocked(
          translated,
          translated[this.source].left,
          translated[this.source].top,
          translated[this.target].left,
          translated[this.target].top,
        )
      ) {
        // but it's far away and some tech/endpoint would block our path for a direct line
        connectOptions.anchors = [
          chooseAnchor(this.source, "left", leftAnchors, alreadyUsedAnchors),
          chooseAnchor(this.target, "left", leftAnchors, alreadyUsedAnchors),
        ]; //console.log("chose left left");
        // NOTE: i didn't find a case where this occurred,
        // but it could be possible that 2 connections on the same column could overlap with this rule
        // in this case, "right,right" should be chosen
      } else {
        // the target is right above us. shoot it.
        connectOptions.anchors = [
          chooseAnchor(this.source, "top", topAnchors, alreadyUsedAnchors),
          chooseAnchor(this.target, "bottom", bottomAnchors, alreadyUsedAnchors),
        ];
        connectOptions.overlays[1][1] = readableVersionOfLabel(
          connectOptions.overlays[1][1],
          alreadyUsedAnchors[this.target].bottom,
        ); //console.log("chose top bottom");
      }
    } // else: default.

    newTree.connect(connectOptions);
  });
}
