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
		data: { id: id }
	}).done(function(mes) {
		if(mes == "done"){
			el.parent().parent().remove();
		}
		else{
			$("#cannotdelete").modal();
		}
	});
});
$("#deleteShiftModal").on('show.bs.modal', function (e) {
	var id = $(e.relatedTarget).data("id");
	$(this).find(".shiftDelBtn").data("id", id);
	el = $(e.relatedTarget);
});


$('.datepicker').datepicker({
    format: 'yyyy-mm-dd',
    startDate: '-3d'
});
