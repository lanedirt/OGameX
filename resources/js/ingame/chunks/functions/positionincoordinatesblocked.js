/**
 * check if a single spot in the coordinates array is blocked by an element. used for edges of the connection line
 * @param coordinates the coordinates matrix data
 * @param left x-coordinate to check
 * @param top y-coordinate to check
 * @returns {boolean}
 */

function positionInCoordinatesBlocked(coordinates, left, top) {
  for (var i in coordinates) {
    if (coordinates[i].left == left && coordinates[i].top == top) {
      return true;
    }
  }

  return false;
}
