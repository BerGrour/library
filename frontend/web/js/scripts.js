function OpenWindow(URL, Name, Width, Height) {
	NewWindow = window.open(URL, Name, 'minimize=0, maximize=0, toolbar=no, scrollbars=yes, location=no, status=no, menubar=no, resizable=no, width='+Width+', height='+Height);
	return true;
}

function UpdatePjax(pjaxId, async = true) {
    if(document.getElementById(pjaxId)!= null){ 
        $.pjax.reload('#' + pjaxId, {
            method: 'POST',
            timeout: 100,
            async: async,
        });
    } else {
        return 0;
    }
}


function showAlert(type, message) {
    if (message) {
        $(`#alertdiv-${type}`).find('span').text(message);
        $(`#alertdiv-${type}`).fadeIn().delay(3000).fadeOut();
    } else $(`#alertdiv-${type}`).fadeOut();
}


function getSelectedUserInfo(userId) {
    $.ajax({
        url: '/cart/get-cart-list?user_id=' + userId,
        method: 'POST',
        success: function(data) {
            $('#selected-user-info').html(data);
        },
        error: function (xhr, ajaxOptions, thrownError){
            showAlert('error', 'Возникла ошибка! (' + thrownError + ')');
        }
    });
}


function updateOffcanvasContent(userId) {
    var openAccordionIds = [];
    $('.accordion .accordion-collapse').each(function(index) {
        if ($(this).hasClass('show')) {
            openAccordionIds.push($(this).data('cart-id'));
        }
    });

    UpdatePjax('pjax-cart-gridview');
    if (userId) getSelectedUserInfo(userId);

    openAccordionIds.forEach(function(id) {
        var $panels = $('#collapse' + id).collapse({toggle: false});
        $panels.collapse('toggle');
    });
}


function chooseUserForUpdate(userId) {
    $.ajax({
        url: '/user/update?user_id=' + userId,
        method: 'POST',
        success: function(data) {
            $('#update-user-form').html(data);
        },
        error: function (xhr, ajaxOptions, thrownError){
            showAlert('error', 'Возникла ошибка! (' + thrownError + ')');
        }
    });
}


$(document).on('change', 'select', function() {
    $('.select2-selection__rendered').removeAttr('title');
});


$(document).on('click', '#myTab', function() {
    var firstTabEl = document.querySelector('#myTab li:last-child a')
    var firstTab = new bootstrap.Tab(firstTabEl)
    firstTab.show()
});


$('input[name="InventoryBookSearch[filterType]"]').change(function(){
    var value = $(this).val();
    if(value === "filter1") {
        $('#filter1').show();
        $('#filter2').hide();
    } else if (value === "filter2") {
        $('#filter1').hide();
        $('#filter2').show();
    }
});


$(document).on('click', '#createCartButton', function() {
    $('#createCartModal').modal('show');
});


$(document).on('click', '#createNewCartButton', function() {
    var cartName = $('#cartNameInput').val().trim();
    if (cartName.length > 0) {
        $.ajax({
            url: '/cart/create?name=' + cartName,
            type: 'POST',
            success: function(data) {
                if (data.success) {
                    $('#createCartModal').modal('hide');
                    UpdatePjax('pjax-cart-gridview', false);
                    UpdatePjax('pjax-modal-cart', false);
                    let input = document.getElementById('cartNameInput');
                    input.value = '';
                    showAlert('success', `Подборка "${cartName}" успешно создана.`);
                }
            },
            error: function (xhr, ajaxOptions, thrownError){
                showAlert('error', 'Возникла ошибка! (' + thrownError + ')');
            }
        });
    } else showAlert('alert', `Неподходящее название для подборки.`);
});


$(document).on('click', '#addToCartModal', function() {
    var bulk = $(this).data('bulk');
    if (bulk) {
        var keys = $('#grid-view--edition').yiiGridView('getSelectedRows');
        if (keys < 1) showAlert('alert', 'Выберите издания.');
        else $('#selectCartModal').modal('show');
    } else $('#selectCartModal').modal('show');
});


$(document).on('click', '#deleteCartButton', function() {
    var cartId = $(this).data('cart-id');
    var cartName = $(this).data('cart-name');

    if (confirm(`Вы уверены что хотите удалить подборку "${cartName}"?`)) {
        $.ajax({
            url: '/cart/delete?id=' + cartId,
            type: 'POST',
            success: function(data) {
                UpdatePjax('pjax-cart-gridview', false);
                UpdatePjax('pjax-modal-cart', false);
                showAlert('success', `Подборка "${cartName}" была удалена.`);
            },
            error: function (xhr, ajaxOptions, thrownError){
                showAlert('error', 'Возникла ошибка! (' + thrownError + ')');
            }
        });
    } else return false;
});


$(document).on('click', '.removeFromCartButton', function() {
    var cartId = $(this).data("cart-id");
    var keys = $('#gridview-cart_' + cartId).yiiGridView('getSelectedRows');
    var userId = $('#find-user-select').val();

    if (keys.length > 0) {
        $.ajax({
            url: '/cart/delete-selected?cart_id=' + cartId,
            method: 'POST',
            data: {selection: keys},
            success: function(data) {
                if (data.success) {
                    updateOffcanvasContent(userId);
                    showAlert('success', 'Выбранные издания были удалены из подборки.');
                }
            },
            error: function (xhr, ajaxOptions, thrownError){
                showAlert('error', 'Возникла ошибка! (' + thrownError + ')');
            }
        });
    } else showAlert('alert', 'Выберите издания.');
});


$(document).on('click', '.giveToLogbook', function() {
    var userId = $('#find-user-select').val();
    var cartId = $(this).data("cart-id");
    var keys = $('#gridview-cart_' + cartId).yiiGridView('getSelectedRows');
    if (userId) {
        if (keys.length > 0) {
            $.ajax({
                url: '/logbook/give-from-cart?user_id=' + userId,
                method: 'POST',
                data: {selection: keys},
                success: function(data) {
                    if (data.success) {
                        updateOffcanvasContent(userId);
                        showAlert('success', 'Издания были успешно выданы.');
                    }
                },
                error: function (xhr, ajaxOptions, thrownError){
                    showAlert('error', 'Возникла ошибка! (' + thrownError + ')');
                }
            });
        } else showAlert('alert', 'Выберите издания.');
    } else showAlert('alert', 'Выберите пользователя.');
});

$(document).on('click', '.returnEditions', function() {
    var userId = $('#find-user-select').val();
    var keys = $('#gridview-logbook-user_' + userId).yiiGridView('getSelectedRows');
    if (keys.length > 0) {
        $.ajax({
            url: '/logbook/return-editions',
            method: 'POST',
            data: {selection: keys},
            success: function(data) {
                if (data.success) {
                    updateOffcanvasContent(userId);
                    showAlert('success', 'Издания были успешно возвращены.');
                }
            },
            error: function (xhr, ajaxOptions, thrownError){
                showAlert('error', 'Возникла ошибка! (' + thrownError + ')');
            }
        });
    } else showAlert('alert', 'Выберите издания.');
});

$(document).on('click', '.changeOwner', function() {
    var userId = $('#find-user-select').val();
    var keys = $('#gridview-logbook-user_' + userId).yiiGridView('getSelectedRows');
    if (keys.length > 0) $('#changeOwnerModal').modal('show');
    else showAlert('alert', 'Выберите издания.');
});


$(document).on('click', '#actionChangeOwner', function(event) {
    var oldUser = $('#find-user-select').val();
    var newUser = $('#user-change-owner-modal').val();
    var keys = $('#gridview-logbook-user_' + oldUser).yiiGridView('getSelectedRows');
    if (newUser) {
        if (newUser != oldUser) {
            $.ajax({
                url: '/logbook/change-owner?new_user=' + newUser,
                method: 'POST',
                data: {selection: keys},
                success: function(data) {
                    if (data.success) {
                        updateOffcanvasContent(newUser);
                        $('#find-user-select').val(newUser).trigger('change');
                        $('#changeOwnerModal').modal('hide');
                        showAlert('success', 'Издания были успешно переписаны.');
                    }
                },
                error: function (xhr, ajaxOptions, thrownError){
                    showAlert('error', 'Возникла ошибка! (' + thrownError + ')');
                }
            });
        } else showAlert('alert', 'Нельзя выдать тому же пользователю.');
    } else showAlert('alert', 'Выберите нового пользователя.');
});

$(document).ready(function() {
    $('.user-fio').click(function() {
        var userId = $(this).data('user-id');
        $('#offcanvasRight').offcanvas('show');
        $('#find-user-select').val(userId).trigger('change');
        getSelectedUserInfo(userId);
    });
});


$(document).on("click", "#btn-reset", function() {
    $(".with-reset").find(":input").not(":button, :submit, :reset, :hidden, :checkbox, :radio").val("");
    $(".with-reset").find("select").val(null).trigger("change");
});


$(document).on('pjax:send', function() {
    $('#loader').show();
});
$(document).on('pjax:success', function() {
    $('#loader').hide();
});

$(document).on('beforeSubmit', '#global-search', function() {
    $('#loader').show();
});
$(document).on('afterSubmit', '#global-search', function() {
    $('#loader').hide();
});



$(document).on('click', '.grid-view .page-link', function(event) {
    if ($("#grid-view--edition").find("input[type=checkbox]:checked").length > 0) {
        if (!confirm("Выбранные издания не сохранятся, вы уверены, что хотите перейти на другую страницу?")) {
            event.preventDefault();
        }
    }
});

$(document).on('click', '#bulkAddCart', function(event) {
    var cartId = $(this).data('cart');
    var userId = $(this).data('user');
    var keys = $('#grid-view--edition').yiiGridView('getSelectedRows');
    var labels = [];

    if (keys.length > 0)  {
        for (var i = 0; i < keys.length; i++) {
            var values = keys[i].split(';');
            labels.push([values[0], values[1]]);
        }
        $.ajax({
            url: '/cart/bulk-add?cart_id=' + cartId,
            type: 'POST',
            data: {selection: labels},
            success: function(data) {
                if (data.success) {
                    updateOffcanvasContent(userId);
                    $('#selectCartModal').modal('hide');
                    showAlert('success', 'Издания успешно добавлены в корзину.');
                }   
            },
            error: function (xhr, ajaxOptions, thrownError){
                showAlert('error', 'Возникла ошибка! (' + thrownError + ')');
            }
        });
    } else showAlert('alert', 'Выберите издания.');
})

$(document).on('click', '#giveToLogbookModal', function() {
    var bulk = $(this).data('bulk');
    if (bulk) {
        var keys = $('#grid-view--edition').yiiGridView('getSelectedRows');
        if (keys.length > 0) $('#selectLogbookModal').modal('show');
        else showAlert('alert', 'Выберите издания.');
    } else $('#selectLogbookModal').modal('show');
});

$(document).on('click', '#bulkAddLogbook', function(event) {
    var userId = $('#user-select-logbook-modal').val();
    var keys = $('#grid-view--edition').yiiGridView('getSelectedRows');
    var labels = [];

    if (keys.length < 1) {
        showAlert('alert', 'Выберите издания.');
    } else {
        if (userId) {
            for (var i = 0; i < keys.length; i++) {
                var values = keys[i].split(';');
                labels.push([values[0], values[1]]);
            }
            $.ajax({
                url: '/logbook/bulk-add?user_id=' + userId,
                type: 'POST',
                data: {selection: labels},
                success: function(data) {
                    if (data.success) {
                        updateOffcanvasContent(userId);
                        $('#find-user-select').val(userId).trigger('change');
                        $('#selectLogbookModal').modal('hide');
                        showAlert('success', 'Издания успешно выданы.');
                    }   
                },
                error: function (xhr, ajaxOptions, thrownError){
                    showAlert('error', 'Возникла ошибка! (' + thrownError + ')');
                }
            });
        } else showAlert('alert', 'Выберите пользователя.');
    }
})

$(document).on('click', '#addLogbook', function(event) {
    var userId = $('#user-select-logbook-modal').val();
    if (userId) {
        var modelType = $(this).data('type');
        var modelId = $(this).data('id');
        var url = '/logbook/add?user_id=' + userId + "&model_type=" + modelType + '&model_id=' + modelId;
        window.location.href = url;
    } else showAlert('alert', 'Выберите пользователя.');
})


$(document).on('change', '#rubric-inforeleases-select', function() {
    var rubricId = $(this).val();
    var urlParams = new URLSearchParams(window.location.search);
    var id = urlParams.get('id');
  
    $.ajax({
        url: '/seria/inforeleases-list?id=' + id + '&rubric_id=' + rubricId,
        success: function(data) {
            $('#accordion-inforeleases-list').html(data);
        }
    });
});

$('input[name="Infoarticle[type]"]').change(function(){
    var value = $(this).val();
    if (value == 1) {
        $('#field-resource').show();
        $('#field-recieptdate').show();
        $('#field-reciept_year').hide();
        $('#field-source').hide();
    } else if (value == 2) {
        $('#field-resource').show();
        $('#field-recieptdate').hide();
        $('#field-reciept_year').show();
        $('#field-source').hide();
    } else if (value == 3) {
        $('#field-resource').hide();
        $('#field-recieptdate').show();
        $('#field-reciept_year').hide();
        $('#field-source').show();
    }
});