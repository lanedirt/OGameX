PercentSelector.createOverlay = function (bar) {
  var $bar = $(bar);
  $overlay = $bar.children(".PBoverlay");
  var width = $overlay.innerWidth();
  var height = $overlay.innerHeight();
  var canvas = $overlay.get(0);
  canvas.width = width;
  canvas.height = height;
  var ctx = canvas.getContext("2d"); //Create the plastic overlay

  var lingrad = ctx.createLinearGradient(0, 0, 0, height);
  lingrad.addColorStop(0, "rgba(0,0,0,0.05)");
  lingrad.addColorStop(1, "rgba(0,0,0,0.3)");
  ctx.fillStyle = lingrad;
  ctx.fillRect(0, 0, width, height);
  ctx.clearRect(3, 3, width - 6, height - 6);
  lingrad = ctx.createLinearGradient(0, 0, 0, height);
  lingrad.addColorStop(0, "rgba(0,0,0,0.2)");
  lingrad.addColorStop(1, "rgba(0,0,0,0.05)");
  ctx.fillStyle = lingrad;
  ctx.fillRect(3, 3, width - 6, height - 6); //create the steps

  var step = $bar.attr("step");
  if (!step) step = 100;
  ctx.lineWidth = 1;
  var maxWidth = width / step;
  var stepWith = width / step;

  for (var curStep = 0; curStep * step < 100; curStep += 1) {
    var x = Math.floor((curStep * step * width) / 100) - 0.5;
    ctx.beginPath();
    ctx.font = "12px serif";
    ctx.fillStyle = "white";
    ctx.strokeStyle = "black";
    ctx.textAlign = "center";
    ctx.textBaseline = "middle";
    ctx.fillText(((curStep + 1) * 10).toString(), x + stepWith / 2, height / 2, maxWidth); // ctx.strokeText(((curStep + 1) * 10).toString(), x + stepWith/2, height * 1, maxWidth);

    ctx.moveTo(x, height);
    ctx.lineTo(x, height * 0.75);
    ctx.closePath();
    ctx.stroke();
  }
};
