function initTooltipSkins() {
  jQuery.extend(Tipped.Skins, {
    cloud: {
      offset: {
        x: 0,
        y: -1,
        mouse: {
          x: -12,
          y: -12,
        }, // only defined in the base class
      },
      stem: {
        height: 6,
        width: 11,
        offset: {
          x: 5,
          y: 5,
        },
        spacing: 0,
      },
    },
    premium: {
      offset: {
        x: 0,
        y: -1,
        mouse: {
          x: -12,
          y: -12,
        }, // only defined in the base class
      },
      stem: {
        height: 6,
        width: 11,
        offset: {
          x: 5,
          y: 5,
        },
        spacing: 0,
      },
    },
  });
}
