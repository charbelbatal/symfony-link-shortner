
import $ from 'jquery';
import 'popper.js';
import 'bootstrap';
import 'bootstrap/js/dist/toast';
import 'datatables.net';
import 'datatables.net-bs4';
import 'datatables.net-responsive';
import 'jquery-validation';
import 'jquery-validation/dist/additional-methods';
import '@fortawesome/fontawesome-free/js/all';
import 'datatables.net-bs4/css/dataTables.bootstrap4.css';
import 'datatables.net-responsive-bs4/css/responsive.bootstrap4.css';
import './jquery.global';
import './styles/app.scss';


window.app = {
    pageName: '',
    loaderId: "#ajaxLoader"
};

window.app.pageName = $('body').data('pageName');

switch (window.app.pageName) {
    case 'homepage':
        $("#shortenUrlForm").addFormValidation();
        break;
    case 'link-list':
        const $linkListDataTable = $("#link-list");
        $linkListDataTable.DataTable({
            responsive: true,
            autoWidth: false,
            ordering: true,
            orderCellsTop: true,
            pageLength: 10,
            order: [[0, "asc"]],
            dom: "<'row'<'col-sm-12 col-md-6'l>><'row'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            ajax: {
                "url": $linkListDataTable.data('ajaxUrl'),
                "type": "POST"
            },
            columns: [
                {"data": "id", "name": "Id", "class": "text-right"},
                {
                    "data": "url", "name": "URL", "type": "int", "class": "original-link",
                    render: function (data, type, row, meta) {
                        return "<a href='" + data + "' target='_blank'>"+data+"</a>";
                    }
                },
                {
                    "data": "shortUrl", "name": "Short Url", "type": "int",
                    render: function (data, type, row, meta) {
                        return "<a href='" + data + "' target='_blank'>"+data+"</a>";
                    }
                },
                {"data": "hits", "name": "Hits", "type": "int"},
                {"data": "ipAddress", "name": "IP", "type": "int"},
                {"data": "createdAt", "name": "Created (EST)", "type": "int"},
                {"data": "updatedAt", "name": "Updated (EST)", "type": "int"},
                {
                    "name": "Actions",
                    "data": "actions",
                    "orderable": false,
                    render: function (data, type, row, meta) {
                        return "<a href='"+row.actions.view+"' class='pr-1' data-bs-toggle='tooltip' data-bs-placement='top' title='View Link Details'><i class='far fa-eye'></i></a>" +
                            "<a href='"+row.actions.view+"' class='pr-1' data-bs-toggle='tooltip' data-bs-placement='top' title='Edit Link'><i class='fas fa-edit'></i></a>" +
                            "<a href='"+row.shortUrl+"' target='_blank' data-bs-toggle='tooltip' data-bs-placement='top' title='Trigger Short Link'><i class='fas fa-external-link-alt' ></i></a>"
                            ;
                    }
                }
            ],
            initComplete: function () {
                $('[data-bs-toggle="tooltip"]').tooltip();
            }
        });
        break;
    case 'link-view':
    case 'link-edit':
        $('.toast-success').toast('show');
        const $toastMessage = $(".toast-message");

        //<editor-fold desc="Add Validation on Edit Page">
        if($("#shortenUrlForm").length) {
            $("#shortenUrlForm").addFormValidation();
        }
        //</editor-fold>

        //<editor-fold desc="Link Stats DataTable">
        const $linkStatsListDataTable = $("#link-stats-list");
        $linkStatsListDataTable.DataTable({
            responsive: true,
            autoWidth: false,
            ordering: true,
            orderCellsTop: true,
            pageLength: 10,
            order: [[4, "desc"]],
            dom: "<'row'<'col-sm-12 col-md-6'l>><'row'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            processing: true,
            serverSide: true,
            ajax: {
                "url": $linkStatsListDataTable.data('ajaxUrl'),
                "type": "POST"
            },
            columns: [
                {"data": "ipAddress", "name": "IP"},
                {"data": "browser", "name": "Browser"},
                {"data": "device", "name": "Device"},
                {"data": "os", "name": "os"},
                {"data": "createdAt", "name": "Created (EST)"}
            ],
            initComplete: function () {}
        });
        //</editor-fold>

        //<editor-fold desc="Add Are You Sure confirm window on Job Remove from QUEUE Through AJAX Call">
        $('#linkRemovalModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var targetUrl = button.data('deleteUrl');
            var mainUrl = button.data('mainUrl');
            var modal = $(this);

            modal.find("#link-info").html(mainUrl);

            //Add Event to Yes Button Click to Remove the Asset
            modal.off('click', '#btn-yes')
                .on('click', '#btn-yes', function (e) {
                    e.preventDefault();
                    $.ajax({
                        url: targetUrl,
                        type: "DELETE",
                        contentType: false,
                        processData: false,
                        cache: false,
                        dataType: "json",
                        beforeSend: function () {
                            $(window.app.loaderId).show();
                        },
                        error: function (err) {
                            $(window.app.loaderId).hide();
                            $toastMessage.find('.toast-body').html('Error Occurred. Please try again!');
                            $toastMessage.toast('show');
                        },
                        success: function (data) {
                            switch (data.status) {
                                case "success":
                                    $toastMessage.find('.toast-body').html('Deletion was successful. You will be redirected in 2 seconds.');
                                    $toastMessage.toast('show');
                                    setTimeout(function () {
                                        location.href = data.redirectUrl;
                                    },2000);
                                    break;
                                case "error":
                                    $(window.app.loaderId).hide();
                                    $toastMessage.find('.toast-body').html('Error Occurred. Please try again!');
                                    $toastMessage.toast('show');
                                    break;
                            }
                        },
                        complete: function () {
                            modal.modal('hide');
                        }
                    });
                });
        });
        //</editor-fold>
        break;
    default:
        break;
}



