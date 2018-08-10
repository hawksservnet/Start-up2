$(document).ready(function() {
	var switched=false;
	var updateTables_01 = function() {
		if(($(window).width()<767)&&!switched) {
			switched=true;
			$("table.responsive:not(.respon-tbl)").each(function(i,element){
				splitTable($(element));
			});
			return true;
		}else if(switched&&($(window).width()>767)){
			switched=false;
			$("table.responsive:not(.respon-tbl)").each(function(i,element){
				unsplitTable($(element));
			});
		}
	};
	var updateTables_02 = function() {
		$("table.respon-tbl").each(function(i,element){splitTable($(element));});
	};
	$(window).load(updateTables_01);
	$(window).load(updateTables_02);
	$(window).bind("resize",updateTables_01);
	function splitTable(original){
		original.wrap("<div class='table-wrapper' />");
		var copy=original.clone();
		copy.find("td:not(:first-child), th:not(:first-child)").css("display","none");
		copy.removeClass("responsive");
		original.closest(".table-wrapper").append(copy);
		copy.wrap("<div class='pinned' />");
		original.wrap("<div class='scrollable' />");
	}
	function unsplitTable(original){
		original.closest(".table-wrapper").find(".pinned").remove();
		original.unwrap();original.unwrap();
	}
});