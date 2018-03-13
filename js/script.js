function change_iframe(link) {
    $('.menu-navi .active').removeClass('active');
    
    $('#iframeMain').attr('src', $(link).attr('data-url'));
    $(link).parent().addClass('active');
}

function teamModifyMenu() {
    $('.check-box').css('display', 'block');
    
    $('.select-all').css('display', 'block');
    $('.select-delete').css('display', 'block');
}

function teamCheckAll(all) {
    if( all == 1 ) {
        $("#team_check_all").prop("checked", true)
        $(".team_check").prop("checked",true);
    } else {
        if($("#team_check_all").prop("checked")){
            $(".team_check").prop("checked",true);
        }else {
            $(".team_check").prop("checked",false);
        }
    }
}

function submitForm(action) {
    if( action == 'team_delete' ) {
        $('form#team-list-form')[0].submit();
    }
    
    if( action == 'team_modify' ) {
        $('form#teamInfoForm')[0].submit();
    }
}

function teamView(id) {
    $('#modal-team-title').html(id);
    $('#team-name-input').val(id);
    
    document.teamDetailForm.target = "teamDetailIframe";
    document.teamDetailForm.method = "post";
    document.teamDetailForm.action = "team_info_modal.php";
    document.teamDetailForm.submit();
}

function closeModal() {
    $('#teamModal').css('display', 'hide');
}