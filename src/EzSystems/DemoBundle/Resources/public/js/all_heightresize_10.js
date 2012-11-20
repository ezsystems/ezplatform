/* HEIGHT RESIZE - 20060522 */

function initHeightResize() /* _Measurement_element_ID_, _Target_element_ID_ , _Padding-bottom_adjustment_value_(em)_ , _Height_adjustment_value_(px)_ , _IE_height_adjustment_value_(px)_ */
{
    forceHeightResize( [ '-', 'columns', 0, 25, 25, 'heightresize-sidemenu', 'sidemenu', 0, 20, 20, 'heightresize-extrainfo', 'extrainfo', 0, 20, 20, 'heightresize-main', 'main', 0, 20, 20 ] ); 
}

function forceHeightResize( elements )
{
    var element, i, maxSize = 0;

    if ( !document.getElementById )
    {    
        return;
    }

    for ( i = 0; i < elements.length; i += 5 ) /* Find the tallest height */
    {
        element = document.getElementById( elements[ i ] );

        if ( element )
        {
            if ( element.offsetHeight > maxSize )
            {    
                maxSize = element.offsetHeight;
            }
        }
    }

    for ( i = 0; i < elements.length; i += 5 ) /* Resize all heights */
    {
        element = document.getElementById( elements[ i + 1 ] );

        if ( element )
        {
            if ( window.getComputedStyle ) /* If not IE */
            {    
                element.style.paddingBottom = elements[ i + 2 ] + 'em'; /* Padding-bottom adjustment (em) */
                element.style.height = ( maxSize + elements[ i + 3 ] ) + 'px'; /* Height adjustment (px) */
            }
            else /* IE */
            {
                if( /MSIE 7/.test( navigator.appVersion ) ) /* Only apply for IE 7 */
                {
                    element.style.paddingBottom = elements[ i + 2 ] + 'em'; /* Padding-bottom adjustment (em) */
                }
                element.style.height = ( maxSize + elements[ i + 4 ] ) + 'px'; /* Height adjustment (px) */
            }

        }
    }
}

window.onload = window.onresize = initHeightResize;
