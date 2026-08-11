/**
 * check single line of the matrix if a line could be drawn. does not check start or end of the line
 * @param coordinates array containing the positions of the endpoints
 * @param sourceLeft int x-coordinate of the source
 * @param sourceTop int y-coordinate of the source
 * @param targetLeft int x-coordinate of the target
 * @param targetTop int y-coordinate of the target
 * @return bool if the path is blocked by an element
 */

function lineInCoordinatesBlocked(coordinates, sourceLeft, sourceTop, targetLeft, targetTop) {
  if (sourceLeft == targetLeft) {
    // check column (target is above. every time!)
    for (var i in coordinates) {
      if (coordinates[i].left == sourceLeft && sourceTop > coordinates[i].top && targetTop < coordinates[i].top) {
        return true;
      }
    }
  } else if (sourceTop == targetTop && sourceLeft > targetLeft) {
    // check row to the left
    for (var j in coordinates) {
      if (coordinates[j].top == sourceTop && sourceLeft > coordinates[j].left && targetLeft < coordinates[j].left) {
        return true;
      }
    }
  } else if (sourceTop == targetTop && sourceLeft < targetLeft) {
    // check row to the right
    for (var k in coordinates) {
      if (coordinates[k].top == sourceTop && sourceLeft < coordinates[k].left && targetLeft > coordinates[k].left) {
        return true;
      }
    }
  }

  return false;
}
