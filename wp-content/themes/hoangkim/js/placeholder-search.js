const placeholders = ["nhập hàng trung quốc", "bảng giá nhập hàng", "cách đặt hàng taobao"];

$(document).ready(async function () {
  async function printPlaceholder(string) {
    let i = 0;
    let isUp = true;
    let str = string.slice(0, i);

    while (true) {
      str = string.slice(0, i);
      $("input.header-input.input-search").attr("placeholder", str);
      if (!isUp && i === 0) break;
      if (isUp) i++;
      else i--;
      if (str === string && isUp) {
        isUp = false;
        await new Promise((res) => setTimeout(res, 1000));
      }
      await new Promise((res) => setTimeout(res, 130));
    }
  }

  let i = 0;
  while (true) {
    await printPlaceholder(placeholders[i]);
    i++;
    if (i === placeholders.length) i = 0;
  }
});
