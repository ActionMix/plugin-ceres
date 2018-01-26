var widgetTemplates;

$(document).ready(function()
{
    initCeresForGridstack();
});

function initCeresForGridstack()
{
    initWidgetTemplates();
    removeDefaultLinks();
    injectGridstackMarkup();
    addBackendEventListeners();
}

function initWidgetTemplates()
{
    widgetTemplates = {
        'Image Slider': [$('#carousel-example').clone(), $('#carousel-example').height()],
        'Category Highlight': [$('#recommended-plugins').parent().parent().clone(), $('#recommended-plugins').parent().parent().height()]
    };
}

function addContextMenu(element)
{
    $(element).append('<div class="context-menu"></div>');

    addEditButton(element);
    addDeleteButton(element);
}

function addDeleteButton(element)
{
    $(element).find('.context-menu').append('<div class="shopbuilder-icon delete-icon fa fa-trash"></div>');
    $(element).mouseenter(function ()
    {
        $(this).find('.context-menu').css('display','block');
    });

    $(element).mouseleave(function ()
    {
        $(this).find('.context-menu').css('display','none');
    });

    $(element).find('.delete-icon').click(function ()
    {
        // todo: @vwiebe fix dropzone scope
        $('.grid-stack-0').data('gridstack').removeWidget($(this).closest('.grid-stack-item'));
    });
}

function addEditButton(element)
{
    $(element).find('.context-menu').append('<div class="shopbuilder-icon edit-icon fa fa-pencil"></div>');
    $(element).find('.edit-icon').click(function ()
    {
        var propertiesObject = {
            widgetWidth: {
                controlType:"inputNumber",
                options:{
                    label:"Breite",
                    required:false
                }
            },
            widgetHeadline: {
                controlType:"inputText",
                options:{
                    label:"Header",
                    required:false
                }
            }
        };

        $('body').trigger('shopbuilder_open_properties', propertiesObject);
    });
}

function addBackendEventListeners()
{
    $('body').on('shopbuilder_drop', function(element)
    {
        addContentWidget(element.originalEvent.detail.identifier);
    });

    $('body').on('shopbuilder_reset', function()
    {
        $('body').html('');
        $('body').addClass('loading');

        window.location.reload(true);
    });

    $('body').on('shopbuilder_zoom', function(event)
    {
        var value = event.originalEvent.detail.value;
        $('body').css('zoom', value * 100 + '%')
    });

    $('body').on('shopbuilder_open_properties', function(event, object)
    {
        console.log(object);
    });

    // test
    // $('.brand-wrapper').append('<button id="testEventButton">trigger event</button>');
    // $('#testEventButton').on('click', function ()
    // {
    //     $('body').trigger('shopbuilder_zoom', 0.5);
    // });
}

function addContentWidget(element)
{
    var object = widgetTemplates[element][0];
    var height = widgetTemplates[element][1];

    var gridStackItem = $(  '<div class="grid-stack-item"' +
        '     data-gs-height="' + Math.round(height / 40) + '"><div class="grid-stack-item-content">' + $(object).html() + '</div>' +
        '</div>');

    setDragCursorToChildElements(gridStackItem);
    addContextMenu(gridStackItem);

    $('.grid-stack-0').data('gridstack').addWidget(gridStackItem);
}

function setDragCursorToChildElements(element)
{
    // iterate over all child elements
    $(element).find('*').each(function()
    {
        $(this).css('cursor','move');
    });
}

function removeDefaultLinks()
{
    // iterate over all body elements
    $('body').find('*').each(function()
    {
        $(this).click(function (event)
        {
            //prevent all existing click actions
            event.preventDefault();
        })
    });
}

function injectGridstackMarkup()
{
    // select drag & drop areas
    $('.mkt-homepage').each(function(i)
    {
        // iterate over all sub-elements
        $(this).find('> *').each(function(j)
        {
            // create gridstack item markup
            var gridStackItem = $(  '<div class="grid-stack-item"' +
                '     data-gs-height="' + Math.round($(this).height() / 40) + '"><div class="grid-stack-item-content"></div>' +
                '</div>');

            // overwrite cursor for all contained elements
            setDragCursorToChildElements($(this));

            // add hover menu to container
            addContextMenu(gridStackItem);

            // wrap current element with gridstack item markup
            $(this).wrap(gridStackItem)

            ++j;
        });

        // add gridstack container class for current drag & drop area
        $(this).addClass('grid-stack-' + i);

        // initialize gridstack for current gridstack container
        initGridstack(i);

        ++i;
    });
}

/**
 *
 * @param id for current container
 */
function initGridstack(id)
{
    var options = {
        width:1,
        cellHeight: 40,
        verticalMargin: 0,
        acceptWidgets: '.grid-stack-item'
    };

    var selector = '.grid-stack-' + id;
    $(selector).gridstack(options);
}