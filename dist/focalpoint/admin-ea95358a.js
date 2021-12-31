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
(window.wpackioresponsivePicsfocalpointJsonp=window.wpackioresponsivePicsfocalpointJsonp||[]).push([[0],[function(e,t,i){i(1),i(2),e.exports=i(3)},function(e,t,i){var a="responsivePicsdist".replace(/[^a-zA-Z0-9_-]/g,"");i.p=window["__wpackIo".concat(a)]},function(e,t){!function(e){var t=this,i={init:function(){var e=getFocalPoint(attachment);console.log(e),i.picker=$image,i.point=$imageFocalPoint,i.x=e.x,i.y=e.y,i.setEventListeners()},setEventListeners:function(){i.picker.on("click",t.setFocalPoint),i.point.draggable({cursor:"move",drag:t.dragging,containment:$imageFocalWrapper})},setFocalPoint:function(a){var n=a.offsetY-i.point.height()/2,o=a.offsetX-i.point.width()/2;i.point.css({top:n,left:o,display:"block"}),i.x=Math.round((a.pageY-e(t).offset().top)/i.picker.height()*100),i.y=Math.round((a.pageX-e(t).offset().left)/i.picker.width()*100)},dragging:function(e){i.x=Math.round(e.target.offsetLeft/i.picker.width()*100),i.y=Math.round(e.target.offsetTop/i.picker.height()*100)}};e(document).ready((function(){var t=function(e){var t=wp.media.template("attachment-select-focal-point"),i=e.find(".thumbnail"),a=e.find(".details-image");t&&(i.prepend(t),$imageFocal=e.find(".image-focal"),$imageFocalWrapper=e.find(".image-focal__wrapper"),$imageFocalPoint=e.find(".image-focal__point"),$imageFocalClickarea=e.find(".image-focal__clickarea"),a.prependTo($imageFocalWrapper),$image=$imageFocalWrapper.find(".details-image"));var n=wp.media.template("attachment-save-focal-point"),o=e.find(".attachment-actions");n&&o.append(n)},a=function(t){$image.on("load",(function(t){return updateFocusInterface(e(t.currentTarget))})),e(window).on("resize",(function(){return updateFocusInterface($image)})),i.init()},n=wp.media.view.Attachment.Details.TwoColumn;wp.media.view.Attachment.Details.TwoColumn=n.extend({initialize:function(){this.model.on("change:compat",this.change,this)},render:function(){wp.media.view.Attachment.prototype.render.apply(this,arguments);var e=this.model.attributes.type;return"image"===e&&(t(this.$el),a(this.model)),this},change:function(){if("image"===this.model.attributes.type){var t=function(t){var i=t.get("compat");if(i.item)return{x:e(i.item).find(".compat-field-responsive_pics_focal_point_x input").val(),y:e(i.item).find(".compat-field-responsive_pics_focal_point_y input").val()}}(this.model);i.x=t.x,i.y=t.y}}})}))}(jQuery)},function(e,t,i){}],[[0,1]]]);
//# sourceMappingURL=admin-ea95358a.js.map