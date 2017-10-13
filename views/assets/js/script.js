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
		var td = $("#e" + employeeId).children()[dates - 1];
			$(td).html($(td).html() + data);
	});
});

$(".checkBtn").click(function(e){
	var date = e.target.dataset["date"];
	var uid = e.target.dataset["uid"];
	var shiftId = e.target.dataset["shiftid"];
	var button = $(this);
	var text = $(button.parent().parent().children()[2]);
	$.ajax({
		method: "POST",
		url: document.location.href,
		data: { date: date, Uid: uid, ShiftId: shiftId }
	}).done(function() {
		
		var isCheck = button.hasClass("btn-info");
		if(isCheck){
			button.removeClass("btn-info");
			button.addClass("btn-danger");
			button.html("Bỏ chấm");
			text.html("đã chấm");
		}
		else{
			button.removeClass("btn-danger");
			button.addClass("btn-info");	
			button.html("Chấm");
			text.html("chưa chấm");
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

$(".salaryByHour").keyup(function(){
	let attendanceHours = $(this).parent().siblings(".AttendanceHour").html();
	let salaryByHour = parseFloat($(this).val().replace(".", "")) || 0;
	let total = numberWithCommas(attendanceHours * salaryByHour);
	$(this).parent().siblings(".totalSalary").html(total + " vnđ");
});

$(".bonusSalary").keyup(function(){
	let currentTotal = parseFloat($(this).parent().siblings(".totalSalary").html().replace(",", "").replace(" vnđ", ""));
	let bonusSalary = parseFloat($(this).val().replace(".", "")) || 0;
	let total = numberWithCommas(currentTotal + bonusSalary);
	$(this).parent().siblings(".totalSalary").html(total + " vnđ");
});

$(".cashAdvance").keyup(function(){
	let currentTotal = parseFloat($(this).parent().siblings(".totalSalary").html().replace(",", "").replace(" vnđ", ""));
	let cashAdvance = parseFloat($(this).val().replace(".", "")) || 0;
	let total = numberWithCommas(currentTotal - cashAdvance);
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
		url: document.location.href + '/delete',
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
		}
	});
});
$(".importBtn").click(function(){
	$("#importProductModal").find(".productId").val($(this).data("id"));
});

$(".exportBtn").click(function(){
	$("#exportProductModal").find(".productId").val($(this).data("id"));
});
$('.datepicker').datepicker({
    format: 'yyyy-mm-dd',
    startDate: '-3d'
});

