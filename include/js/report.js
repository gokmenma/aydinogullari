var forwardButton = $("#forwardtoreport");
var selectElement = $("#reporttype"); // select elementini seç
var selectElement = $("#reporttype"); // select elementini seç

var newreport = $("#report-new");
$(newreport).click(function () {
  var type = newreport.data("type");
  forwardButton.attr("data-type", type);
});

var contentview = $("#content-view");
$(contentview).click(function () {
  var type = contentview.data("type");
  forwardButton.attr("data-type", type);
});

$(forwardButton).click(function () {
  var reporttype = $(selectElement).val(); // select'in değerini al
  var RoutePage = $(selectElement).data("page"); // select'in değerini al
  var type = forwardButton.data("type");

  var selectedOption = $(selectElement).find("option:selected");
  var newPageValue = selectedOption.data("new");
  var viewPageValue = selectedOption.data("view");

  console.log(newPageValue);
  if (type == "new") {
    window.location.href =
      "index.php?p=" + newPageValue + "&type=" + reporttype;
  } else {
    window.location.href =
      "index.php?p=" + viewPageValue + "&type=" + reporttype;
  }
});

$(".closeModal").click(function () {
  $("#reportdetail").modal("hide");
});
