/*!
 * 
 * ResponsivePics
 * 
 * @author Booreiland
 * @version 1.4.0
 * @link https://responsive.pics
 * @license undefined
 * 
 * Copyright (c) 2021 Booreiland
 * 
 * This software is released under the [MIT License](https://github.com/booreiland/responsive-pics/blob/master/LICENSE)
 */
(window.wpackioresponsivePicsfocalpointJsonp=window.wpackioresponsivePicsfocalpointJsonp||[]).push([[0],[function(i,t,e){e(1),e(2),i.exports=e(3)},function(i,t,e){var o="responsivePicsdist".replace(/[^a-zA-Z0-9_-]/g,"");e.p=window["__wpackIo".concat(o)]},function(i,t){!function(i){var t=this,e={init:function(i){var t=e.getFocalPoint(i);e.picker=$image,e.point=$imageFocalPoint,e.x=t.x,e.y=t.y,e.setEventListeners()},setEventListeners:function(){e.picker.on("click",e.setFocalPoint),e.point.draggable({cursor:"move",drag:e.dragging,containment:$imageFocalWrapper})},getFocalPoint:function(t){var e=t.get("compat");if(e.item)return{x:i(e.item).find(".compat-field-responsive_pics_focal_point_x input").val(),y:i(e.item).find(".compat-field-responsive_pics_focal_point_y input").val()}},setFocalPoint:function(o){console.log("setFocalPoint",o);var a=o.offsetY-e.point.height()/2,n=o.offsetX-e.point.width()/2;e.point.css({top:a,left:n,display:"block"}),e.x=Math.round((o.pageY-i(t).offset().top)/e.picker.height()*100),e.y=Math.round((o.pageX-i(t).offset().left)/e.picker.width()*100)},dragging:function(i){console.log("dragging",i),e.x=Math.round(i.target.offsetLeft/e.picker.width()*100),e.y=Math.round(i.target.offsetTop/e.picker.height()*100)}};i(document).ready((function(){var i=function(i){var t=wp.media.template("attachment-select-focal-point"),e=i.find(".thumbnail"),o=i.find(".details-image");t&&(e.prepend(t),$imageFocal=i.find(".image-focal"),$imageFocalWrapper=i.find(".image-focal__wrapper"),$imageFocalPoint=i.find(".image-focal__point"),$imageFocalClickarea=i.find(".image-focal__clickarea"),o.prependTo($imageFocalWrapper),$image=$imageFocalWrapper.find(".details-image"));var a=wp.media.template("attachment-save-focal-point"),n=i.find(".attachment-actions");a&&n.append(a)},t=function(i){$image.on("load",(function(t){return e.init(i)}))},o=wp.media.view.Attachment.Details.TwoColumn;wp.media.view.Attachment.Details.TwoColumn=o.extend({initialize:function(){this.model.on("change:compat",this.change,this)},render:function(){wp.media.view.Attachment.prototype.render.apply(this,arguments);var e=this.model.attributes.type;return"image"===e&&(i(this.$el),t(this.model)),this},change:function(){"image"===this.model.attributes.type&&(focalPoint=e.getFocalPoint(this.model),e.x=focalPoint.x,e.y=focalPoint.y)}})}))}(jQuery)},function(i,t,e){}],[[0,1]]]);
//# sourceMappingURL=admin-b853f712.js.map