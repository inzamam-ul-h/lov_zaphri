(()=>{"use strict";$(".start-time").datetimepicker({format:"YYYY-MM-DD h:mm A",useCurrent:!1,icons:{previous:"icon-arrow-left icons",next:"icon-arrow-right icons"},sideBySide:!0,minDate:moment().subtract(1,"days"),widgetPositioning:{horizontal:"left",vertical:"bottom"}}),$(".members").select2({minimumResultsForSearch:-1,placeholder:Lang.get("messages.placeholder.select_member")}),$(".time-zone").select2({placeholder:Lang.get("messages.placeholder.select_time_zone")}),$("#meetingForm").on("submit",(function(e){return e.preventDefault(),jQuery(this).find("#btnSave").button("loading"),$("#meetingForm")[0].submit(),!0}))})();