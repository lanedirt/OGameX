function getItem(uuid) {
  if (typeof inventoryObj.items_inventory[uuid] != "undefined") {
    return inventoryObj.items_inventory[uuid];
  }

  return null;
}
