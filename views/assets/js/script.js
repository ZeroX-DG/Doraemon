var Uid;
var ShiftDate;
var ShiftId;
var seletectEl;
$('#deleteModal').on('show.bs.modal', function(e) {

    //get data-id attribute of the clicked element
    var button = $(e.relatedTarget);
    seletectEl = button[0];
    Uid = button[0]["dataset"]["uid"];
    ShiftDate = button[0]["dataset"]['date'];
    ShiftId = button[0]["dataset"]['shiftid'];
});

$(".deleteShift").click(function(){
	var base = $("#base").attr("content");
	$.ajax({
		method: "POST",
		url: base + "/schedules/shift/delete",
		data: { uid: Uid, shiftId: ShiftId, date: ShiftDate }
	})
	.done(function() {
		seletectEl.parentElement.parentElement.remove();
		$('.shift-panel:empty').remove();
	});
});

$("#addBtn").click(function(){
	var base = $("#base").attr("content");
	var scheduleId = $("#scheduleId").attr("value");
	var dates = $("#dates").val().replace("Thứ ", "");
	if(dates == "Chủ nhật"){
		dates = 8;
	}
	else{
		dates = parseInt(dates);
	}
	var shift = $("#shifts").val();
	var employeeId = $("#employees").val();
	$.ajax({
		method: "POST",
		url: base + "/schedules/shift/add",
		data: { scheduleId: scheduleId, dayOfWeek: dates, shift: shift, employeeId: employeeId }
	})
	.done(function(data) {
		if(data.indexOf("nope") != -1){
			$("#CantAddShiftModal").modal();
		}
		else{
			var td = $("#e" + employeeId).children()[dates - 1];
			$(td).html($(td).html() + data);
		}
		
	});
});

$(".checkBtn").click(function(e){
	var date = e.target.dataset["date"];
	var uid = e.target.dataset["uid"];
	var shiftId = e.target.dataset["shiftid"];
	var parent = $(e.target).parent().parent();
	var time_in_hour = parent.find(".time_in_hour").val();
	var time_in_minute = parent.find(".time_in_minute").val();
	var time_out_hour = parent.find(".time_out_hour").val();
	var time_out_minute = parent.find(".time_out_minute").val();
	var statusDOM = parent.find(".status");
	var status = "";
	for (var i = 0; i < statusDOM.length; i++) {
		if ($(statusDOM[i]).prop("checked")) {
			status = $(statusDOM[i]).val();
		}
	}
	var note = parent.find(".note").val();
	var button = $(this);
	var text = $(button.parent().parent().children()[2]);
	$.ajax({
		method: "POST",
		url: document.location.href,
		data: { 
			date: date, 
			Uid: uid, 
			ShiftId: shiftId, 
			time_in_hour: time_in_hour,
			time_in_minute: time_in_minute,
			time_out_hour: time_out_hour,
			time_out_minute: time_out_minute,
			status: status,
			note: note
		}
	}).done(function() {
		if(status !== 'check'){
			parent.find(".time_in_hour").val("00");
			parent.find(".time_out_hour").val("00");
			parent.find(".time_in_minute").val("00");
			parent.find(".time_out_minute").val("00");
		}
	});
});
var el;
$(".shiftDelBtn").click(function(e){
	var id = $(this).data("id");
	var button = $(this);
	$.ajax({
		method: "POST",
		url: document.location.href + '/delete',
		data: { id: id },
		success: function(mes) {
			if(mes.indexOf("done") != -1){
				el.parent().parent().remove();
			}
			else{
				$("#cannotdelete").modal();
			}
		}
	});
});
$("#deleteShiftModal").on('show.bs.modal', function (e) {
	var id = $(e.relatedTarget).data("id");
	$(this).find(".shiftDelBtn").data("id", id);
	el = $(e.relatedTarget);
});

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function addCommas(target){
	$(target).val(numberWithCommas($(target).val().replace(",", "")));
}

function oneDot(input) {
    var value = input.value,
        value = value.split('.').join('');

    if (value.length > 3) {
      value = value.substring(0, value.length - 3) + '.' + value.substring(value.length - 3, value.length);
    }

    input.value = value;
}

$(".salaryByHour input").keyup(function(){
	let bonusSalary = parseFloat($(this).parent().siblings(".bonusSalary").find("input").val().replace(".", "")) || 0;
  let salaryByHour = parseFloat($(this).val().replace(".", "")) || 0;
	let attendanceHours = $(this).parent().siblings(".AttendanceHour").html();
	let shipSalary = parseInt($(this).parent().siblings(".shipSalary").html().replace(",", "").replace(" vnđ", ""));
  let cashAdvance = parseFloat($(this).parent().siblings(".cashAdvance").find("input").val().replace(".", "")) || 0;
  let total = numberWithCommas((salaryByHour * attendanceHours) + shipSalary + bonusSalary - cashAdvance);
  $(this).parent().siblings(".totalSalary").html(total + " vnđ");
});

$(".bonusSalary input").keyup(function(){
  let bonusSalary = parseFloat($(this).val().replace(".", "")) || 0;
  let salaryByHour = parseFloat($(this).parent().siblings(".salaryByHour").find("input").val().replace(".", "")) || 0;
  let attendanceHours = $(this).parent().siblings(".AttendanceHour").html();
	let shipSalary = parseInt($(this).parent().siblings(".shipSalary").html().replace(",", "").replace(" vnđ", ""));
	let cashAdvance = parseFloat($(this).parent().siblings(".cashAdvance").find("input").val().replace(".", "")) || 0;
  let total = numberWithCommas((salaryByHour * attendanceHours) + shipSalary + bonusSalary - cashAdvance);
  $(this).parent().siblings(".totalSalary").html(total + " vnđ");
});

$(".cashAdvance input").keyup(function(){
	let bonusSalary = parseFloat($(this).parent().siblings(".bonusSalary").find("input").val().replace(".", "")) || 0;
  let salaryByHour = parseFloat($(this).parent().siblings(".salaryByHour").find("input").val().replace(".", "")) || 0;
	let attendanceHours = $(this).parent().siblings(".AttendanceHour").html();
	let shipSalary = parseInt($(this).parent().siblings(".shipSalary").html().replace(",", "").replace(" vnđ", ""));
  let cashAdvance = parseFloat($(this).val().replace(".", "")) || 0;
  let total = numberWithCommas((salaryByHour * attendanceHours) + shipSalary + bonusSalary - cashAdvance);
  $(this).parent().siblings(".totalSalary").html(total + " vnđ");
});

$("#scheduleDateStart").change(function(){
	let datestr = $(this).val();
	let date = new Date(datestr);
	if(date.getDay() != 1){
		$("#bug").html("<b style=\"color: red\">Ngày bắt đầu phải là thứ 2 !</b><br>");
	}
	else{
		$("#bug").html("");
	}
});

$("#addScheduleForm").on("submit", function(e){
	if($("#bug").html() != ""){
		e.preventDefault();
	}
});
let el1;
$("#deleteAccountModal").on('show.bs.modal', function (e) {
	var id = $(e.relatedTarget).data("id");
	$(this).find(".deleteAccountBtn").data("id", id);
	el1 = $(e.relatedTarget);
});
$(".deleteAccountBtn").click(function(e){
	let id = $(this).data("id");
	$.ajax({
		method: "POST",
		url: document.location.href + '/delete',
		data: { Id: id },
		success: function(mes) {
			if(mes.indexOf("done") != -1){
				el1.parent().parent().remove();
			}
			else{
				$("#cannotdelete").modal();
			}
		}
	});
});

let el2;
$("#deleteProductModal").on('show.bs.modal', function (e) {
	var id = $(e.relatedTarget).data("id");
	$(this).find(".deleteProdcutBtn").data("id", id);
	el2 = $(e.relatedTarget);
});
$(".deleteProdcutBtn").click(function(e){
	let id = $(this).data("id");
	$.ajax({
		method: "POST",
		url: document.location.href + '/product/delete',
		data: { Id: id },
		success: function(mes) {
			if(mes.indexOf("done") != -1){
				el2.parent().parent().remove();
			}
			else{
				$("#cannotdelete").modal();
			}
		}
	});
});

let el3;
$("#deleteStorageModal").on('show.bs.modal', function (e) {
	let id = $(e.relatedTarget).data("id");
	$(this).find(".deleteStorageBtn").data("id", id);
	el3 = $(e.relatedTarget);
});
$(".deleteStorageBtn").click(function(e){
	let id = $(this).data("id");
	$.ajax({
		method: "POST",
		url: document.location.href + '/delete',
		data: { Id: id },
		success: function(mes) {
			if(mes.indexOf("done") != -1){
				el3.parent().parent().remove();
			}
			else{
				$("#cannotdelete").modal();
			}
		}
	});
});

let el4;
$("#cancelShipModal").on('show.bs.modal', function (e) {
	let id = $(e.relatedTarget).data("id");
	$(this).find(".cancelShip").data("id", id);
	el4 = $(e.relatedTarget);
});
$(".cancelShip").click(function(e){
	let id = $(this).data("id");
	$.ajax({
		method: "POST",
		url: document.location.href + '/cancel',
		data: { Id: id },
		success: function(mes) {
			if(mes.indexOf("done") != -1){
				el4.parent().parent().remove();
			}
			else {
				$("#canCancelShip").modal();
			}
		}
	});
});

let el5;
$("#doneShipModal").on('show.bs.modal', function (e) {
	let id = $(e.relatedTarget).data("id");
	$(this).find(".acceptShip").data("id", id);
	el4 = $(e.relatedTarget);
});
$(".acceptShip").click(function(e){
	let id = $(this).data("id");
	$.ajax({
		method: "POST",
		url: document.location.href + '/done',
		data: { Id: id },
		success: function(mes) {
			if(mes.indexOf("done") != -1){
				el4.parent().parent().remove();
			}
			else {
				$("#canAcceptShip").modal();
			}
		}
	});
});
$(".importBtn").click(function(){
	$("#importProductModal").find(".productId").val($(this).data("id"));
  $("#importProductModal").find("#name").val($(this).data("name"));
});

$(".exportBtn").click(function(){
	$("#exportProductModal").find(".productId").val($(this).data("id"));
  $("#exportProductModal").find("#name").val($(this).data("name"));
});
//------------------------------
// Permission management
//------------------------------
$("#staff").on('change', reloadPermission);
function reloadPermission(){
  let currentEmployee = $("#staff").val();
  $.ajax({
    method: "GET",
    url: document.location.href + '/getPermission/' + currentEmployee,
    success: function(list) {
      let checkBoxPermissionList = $(".checkBoxPermission");
      for(let i = 0; i < checkBoxPermissionList.length; i++){
        let checkBox = $(checkBoxPermissionList[i]);
        // if the check box id is match the permission id
        if(list.indexOf(parseInt(checkBox.attr("id"))) != -1){
          checkBox.prop('checked', true);
        }
        // otherwise uncheck the check box that doesn't match
        else{
          checkBox.prop('checked', false);
        }
      }
    }
  });
}
// trigger for first time the permission page load
reloadPermission();
$(".checkBoxPermission").change(function(){
  let currentEmployee = $("#staff").val();
  if(this.checked){
    $.ajax({
      method: "POST",
      url: document.location.href + 
          '/addPermission/' + 
          currentEmployee + 
          '/' +
          this.id,
    });
  }
  else{
    $.ajax({
      method: "POST",
      url: document.location.href + 
          '/removePermission/' + 
          currentEmployee + 
          '/' +
          this.id,
    });
  }
});

function updatePermission(){}
var rad = function(x) {
  return x * Math.PI / 180;
};

var getDistance = function(p1, p2) {
  var R = 6378137; // Earth’s mean radius in meter
  var dLat = rad(p2.lat - p1.lat);
  var dLong = rad(p2.lng - p1.lng);
  var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    Math.cos(rad(p1.lat)) * Math.cos(rad(p2.lat)) *
    Math.sin(dLong / 2) * Math.sin(dLong / 2);
  var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  var d = R * c;
  return d; // returns the distance in meter
};

navigator.geolocation.getCurrentPosition((pos)=>{
  let c_lng = pos.coords.latitude;
  let c_lat = pos.coords.longitude;
  //10.699807, 106.719721
  //10.925218, 106.706398
  //10.925938, 106.691752
  //10.9286513,106.6880603
  //10.786813, 106.666491
  let p1 = {lng: c_lng, lat: c_lat};
  let p2 = {lng: 10.7049882, lat: 106.7365014};
  let dist = getDistance(p1, p2);
  if(dist > 200) {
    $(".distance").html("bạn đang ở quá xa");

    $("#takeAttendance").remove();
  }
  else{
    $(".distance").css("display", "none");
	}
	console.log("Khoảng cách: " + dist);
});
//------------------------
// Salary History
// data example:
// [
//   {
//     uid: 1,
//     work_hour: ...,
//     salary_by_hour: ...,
//     bonus: ...,
//     cash_advance: ...
//   }
// ]
function buildSalaryHistory() {
  let salaryList = $(".salaries").toArray();
  let result = [];
  for(let i of salaryList) {
    let dom = $(i);
    let uid = dom.attr("id");
    let work_hour = dom.find(".AttendanceHour").text().trim();
    let salary_by_hour = dom.find(".salaryByHour input").val().trim().replace(".", "") || 0;
    let bonus = dom.find(".bonusSalary input").val().trim().replace(".", "") || 0;
    let cash_advance = dom.find(".cashAdvance input").val().trim().replace(".", "") || 0;
    let total_salary = dom.find(".totalSalary").text().trim().replace(",", "").replace(" vnđ", "") || 0;
    result.push({uid, work_hour, salary_by_hour, bonus, cash_advance, total_salary});
  }
  return result;
}
function sendSalaryHistory() {
  let salaries = buildSalaryHistory();
  let month = $("#month").val();
  $.ajax({
    method: "POST",
    url: document.location.href + '/save',
    data: { data: salaries, month: month },
    success: () => {
      window.location.href = window.location.href + "/history";
    }
  });
}
$(".scheduleShift").click(function() {
	let checkBox = $(this);
	let scheduleId = checkBox.data("scheduleid");
	let dayOfWeek = checkBox.data("date");
	let shift = checkBox.data("shiftid");
	let employeeId = checkBox.data("userid");
	let url = document.location.href.split("/");
	url.splice(-2, 2);
	url = url.join("/");
	if(checkBox.prop("checked")) {
		$.ajax({
			method: "POST",
			url: url + '/shift/add',
			data: { scheduleId, dayOfWeek, shift, employeeId },
			success: (mes) => {
				if(mes.indexOf("bạn không có quyền vào trang này") != -1) {
					alert("bạn không có quyền chỉnh sửa lich");
					checkBox.prop("checked", false);
				}
			}
		});
	}
	else {
		$.ajax({
			method: "POST",
			url: url + '/shift/delete',
			data: { scheduleId, dayOfWeek, shift, employeeId },
			success: (mes) => {
				if(mes.indexOf("bạn không có quyền vào trang này") != -1) {
					alert("bạn không có quyền chỉnh sửa lich");
					checkBox.prop("checked", false);
				}
			}
		});
	}
});

$(".amount input").keyup(function(){
  let amount = parseInt($(this).val().replace(".", "")) || 0;
  let price = parseFloat($(this).parent().siblings(".price").html().replace(",", "")) || 0;
  let total = numberWithCommas(amount * price);
  $(this).parent().siblings(".total").html(total + " vnđ");
});

function getSaveStorageData(){
  let listOfProduct = $(".product");
  let data = [];
    listOfProduct.each(function(index, item){
    let obj = {};
    let current = $(item);
    let total = current.find(".total").text();
    let productName = current.find(".productName").text();
    let price = current.find(".price").text();
    let amount = current.find(".amount input").val();
    let isHomeStorage = current.find("#home").prop("checked");
    let isWorkStorage = current.find("#work").prop("checked");
    if (isHomeStorage || isWorkStorage && amount != "") {
      obj.productId = current.attr("id");
      obj.productName = productName;
      obj.amount = amount;
      obj.price = price;
      obj.total = total;
      obj.isHomeStorage = isHomeStorage;
      obj.isWorkStorage = isWorkStorage;
      data.push(obj);
    }
  });
  return data;
}

function buildImportListHtml(data) {
  let html = "";
  for (let i = 0; i < data.length; i++) {
    let current = data[i];
    html += "<tr id=" + current.productId + ">";
    html += "<td>" + current.productName + "</td>";
    html += "<td>" + current.price + "</td>";
    let storages = 
      (current.isHomeStorage ? "kho nhà <br>" : "") + 
      (current.isWorkStorage ? "kho quán" : "");
    html += "<td>" + storages + "</td>";
    html += "<td>" + current.amount + "</td>";
    html += "<td>" + current.total + "</td>";
    html += "<td><button class='btn btn-danger' onclick='removeFromImportList(this)'>bỏ nhập</button></td>";
    html += "</tr>";
  }
  return html;
}

function addToImportList() {
  let data = getSaveStorageData();
  let html = buildImportListHtml(data);
  $("#importList").html(html);
}

function removeFromImportList(e) {
  let id = $($(e).parent().parent()).attr("id");
  let productTd = $("#productList #" + id);
  productTd.find("input[type=checkbox]").prop("checked", false);
  productTd.find(".amount input").val("");
  addToImportList();
}

function importProduct() {
  let data = getSaveStorageData();
  //console.log(JSON.stringify(data));
  window.location.href = document.location.href + '/product/import?data=' + JSON.stringify(data);
}

addToImportList();

$('.datepicker').datepicker({
    format: 'yyyy-mm-dd',
    startDate: '-3d'
});

