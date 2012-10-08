/*
 * World's Worst Software Javascript Background Scaler (C) Jason Baker 2007
 *
 * A background scaler that attempts to make the background fill the width
 *   and height of the browser. This library was written for the following sites:
 *     http://www.onejasonforsale.com
 *     http://www.experimentalfutility.com
 * 
 * written by Jason Baker (jason@onejasonforsale.com)
 * on github: https://github.com/codercowboy/web-scripts
 * more info: http://www.codercowboy.com
 *
 * Feel free to use this library on your own sites!
 *
 * For updated versions of the library, visit
 *	   http://www.worldsworstsoftware.com
 *
 * Updates:
 * 
 * 2007/02/28
 *   - Initial Version.
 *
 * Usage Instructions:
 *  1) Link to this library in the HEAD section of your page like so:
 *
 *     <script type="text/javascript" src="wwsbgscaler.js" />
 *
 *  2) Place your background image inside of your BODY section, outside of any DIVs
 *  3) Give your bottom-most div an id (for this example: "content")
 *  4) Code your background IMG tag to call setupImageScaler like so:
 *
 *      <img src="bg.jpg" style="position: absolute; top: 0px; left: 0px; visibility: hidden;"
 *           onLoad='setupImageScaler(this, "content", 100, true, true);'>
 *
 *      Note: 100 in the line above is the pixel offset to add to the bottom dimension
 *            of the content div when calculating the background height. This gives
 *            you the ability to give your bottom most div some space between it and the
 *            bottom of the page.
 *
 *      Note: it is a good idea to start the background image's visibility style
 *            off as "hidden" so non-javascript browsers won't show a unresized
 *            background image.
 *
 * Copyright (c) 2012, Coder Cowboy, LLC. All rights reserved.
 *  
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 * 1. Redistributions of source code must retain the above copyright notice, this
 * list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation
 * and/or other materials provided with the distribution.
 *  
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *  
 * The views and conclusions contained in the software and documentation are those
 * of the authors and should not be interpreted as representing official policies,
 * either expressed or implied.
 */


var imageToResize;
var scaleX;
var scaleY;
var contentDiv;
var divOffset;


function getClientHeight()
{
	return document.documentElement.clientHeight;
}

function getClientWidth()
{
	return document.documentElement.clientWidth;
}

function getDivHeight()
{
	return contentDiv.offsetHeight + (divOffset * 2);
}

function showImage()
{
	imageToResize.style.visibility="visible";
}

function resizeImage()
{
	//do the function twice in case one resize causes a blank space to be left
	//  where scrollbars were before the first resize
	for (var i = 0; i < 2; i++)
	{
		if (scaleX)
		{
			if (imageToResize.width != getClientWidth())
			{
				imageToResize.width = getClientWidth();
			}
		}

		if (scaleY)
		{
			var newHeight = Math.max(getDivHeight(), getClientHeight());

			if (imageToResize.height != newHeight)
			{
				imageToResize.height = newHeight;
			}
		}
	}
	//document.getElementById('bgimage').height =  Math.max(contentHeight, clientHeight);
}

/*
 * image - the image object to scale
 * div id - the id for the div to calculate content height from
 * div_offset - the offset to add to the div height for total content height
 * scale_x - boolean flag specifying whether to scale the image along the x axis or not
 * scale_y - boolean flag specifying whether to scale the image along the y axis or not
 */
function setupImageScaler(image, div_id, div_offset, scale_x, scale_y)
{
	imageToResize = image;
	scaleX = scale_x;
	scaleY = scale_y;
	contentDiv = document.getElementById(div_id);
	divOffset = div_offset;
	resizeImage();
	showImage();

	onresize = function() { resizeImage(); }
	onload = function() { resizeImage(); }
}


