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
(window.wpackioresponsivePicsfocalpointJsonp=window.wpackioresponsivePicsfocalpointJsonp||[]).push([[0],[function(t,e,i){i(1),i(2),t.exports=i(3)},function(t,e,i){var n="responsivePicsdist".replace(/[^a-zA-Z0-9_-]/g,"");i.p=window["__wpackIo".concat(n)]},function(t,e){!function(t){var e=this,i={init:function(t){var n=e.getFocalPoint(t);console.log(n),i.picker=$image,i.point=$imageFocalPoint,i.x=n.x,i.y=n.y,i.setEventListeners()},setEventListeners:function(){i.picker.on("click",e.setFocalPoint),i.point.draggable({cursor:"move",drag:e.dragging,containment:$imageFocalWrapper})},getFocalPoint:function(e){var i=e.get("compat");if(i.item)return{x:t(i.item).find(".compat-field-responsive_pics_focal_point_x input").val(),y:t(i.item).find(".compat-field-responsive_pics_focal_point_y input").val()}},setFocalPoint:function(n){var a=n.offsetY-i.point.height()/2,o=n.offsetX-i.point.width()/2;i.point.css({top:a,left:o,display:"block"}),i.x=Math.round((n.pageY-t(e).offset().top)/i.picker.height()*100),i.y=Math.round((n.pageX-t(e).offset().left)/i.picker.width()*100)},dragging:function(t){i.x=Math.round(t.target.offsetLeft/i.picker.width()*100),i.y=Math.round(t.target.offsetTop/i.picker.height()*100)}};t(document).ready((function(){var e=function(t){var e=wp.media.template("attachment-select-focal-point"),i=t.find(".thumbnail"),n=t.find(".details-image");e&&(i.prepend(e),$imageFocal=t.find(".image-focal"),$imageFocalWrapper=t.find(".image-focal__wrapper"),$imageFocalPoint=t.find(".image-focal__point"),$imageFocalClickarea=t.find(".image-focal__clickarea"),n.prependTo($imageFocalWrapper),$image=$imageFocalWrapper.find(".details-image"));var a=wp.media.template("attachment-save-focal-point"),o=t.find(".attachment-actions");a&&o.append(a)},n=function(e){$image.on("load",(function(e){return updateFocusInterface(t(e.currentTarget))})),t(window).on("resize",(function(){return updateFocusInterface($image)})),i.init(e)},a=wp.media.view.Attachment.Details.TwoColumn;wp.media.view.Attachment.Details.TwoColumn=a.extend({initialize:function(){this.model.on("change:compat",this.change,this)},render:function(){wp.media.view.Attachment.prototype.render.apply(this,arguments);var t=this.model.attributes.type;return"image"===t&&(e(this.$el),n(this.model)),this},change:function(){"image"===this.model.attributes.type&&(focalPoint=i.getFocalPoint(this.model),i.x=focalPoint.x,i.y=focalPoint.y)}})}))}(jQuery)},function(t,e,i){}],[[0,1]]]);
//# sourceMappingURL=admin-18222d8e.js.map