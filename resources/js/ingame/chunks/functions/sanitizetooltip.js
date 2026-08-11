function sanitizeTooltip(text) {
  return text.replace(/<\s*script/g, "&lt;script");
}
