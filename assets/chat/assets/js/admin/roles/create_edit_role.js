(()=>{"use strict";$("#createRoleForm").on("submit",(function(e){if(e.preventDefault(),!(""===$("#role_name").val().trim().replace(/ \r\n\t/g,"")))return jQuery(this).find("#btnCreateRole").button("loading"),$("#createRoleForm")[0].submit(),!0;displayToastr("Error","error","Name field is not contain only white space")})),$("#editRoleForm").on("submit",(function(e){if(e.preventDefault(),!(""===$("#edit_role_name").val().trim().replace(/ \r\n\t/g,"")))return jQuery(this).find("#btnEditSave").button("loading"),$("#editRoleForm")[0].submit(),!0;displayToastr("Error","error","Name field is not contain only white space")}))})();