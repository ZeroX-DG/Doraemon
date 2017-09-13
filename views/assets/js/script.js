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