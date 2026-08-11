function filterPlayerNames(obj) {
  if ($(obj).val().length >= 3) {
    const filtered = Object.values(playerNames)
      .filter((user) => user.name.toLowerCase().includes($(obj).val().toLowerCase()))
      .filter(
        function (user) {
          if (this.count < 5 && user.id > 0) {
            this.count++;
            return true;
          }

          return false;
        },
        {
          count: 0,
        },
      );
    showUsers(filtered);
  }
}
