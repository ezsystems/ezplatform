/* Insert tags supplied as arguments back into main document */

function insertMedia()
{
    for( var k = 0, l = arguments.length; k < l; k++ )
    {
        document.write( arguments[k] );
    }
}
