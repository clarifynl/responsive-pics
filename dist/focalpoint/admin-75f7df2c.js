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
(window.wpackioresponsivePicsfocalpointJsonp=window.wpackioresponsivePicsfocalpointJsonp||[]).push([[0],[function(t,i,n){n(1),n(2),t.exports=n(3)},function(t,i,n){var e="responsivePicsdist".replace(/[^a-zA-Z0-9_-]/g,"");n.p=window["__wpackIo".concat(e)]},function(t,i){var n;(n=jQuery)(document).ready((function(){var t,i,e,o=function(i){t.addClass("is-dragging")},a=function(t){console.log("draggingFocalPoint",t.target)},c=function(i){t.removeClass("is-dragging")},p=function(t){t.stopPropagation(),t.preventDefault()},s=function(t){t.stopPropagation(),t.preventDefault(),console.log("dropFocalPoint",e.position()),i.append(e)},d=function(n){var o=wp.media.template("attachment-select-focal-point"),a=n.find(".thumbnail"),c=n.find(".details-image");o&&(a.prepend(o),t=n.find(".image-focal"),i=n.find(".image-focal__wrapper"),e=n.find(".image-focal__point"),n.find(".image-focal__clickarea"),c.prependTo(i));var p=wp.media.template("attachment-save-focal-point"),s=n.find(".attachment-actions");p&&s.append(p)},r=function(t){t.id;var d,r,l=t.get("compat");if(l.item){var f=n(l.item).find(".compat-field-responsive_pics_focal_point_x input").val(),m=n(l.item).find(".compat-field-responsive_pics_focal_point_y input").val();d=f,r=m,e.css({left:"".concat(d,"%"),top:"".concat(r,"%"),display:"block"}),i.on("dragover",p),i.on("drop",s),e.on("dragstart",o),e.on("drag",a),e.on("dragend",c)}},l=wp.media.view.Attachment.Details.TwoColumn;wp.media.view.Attachment.Details.TwoColumn=l.extend({initialize:function(){this.model.on("change:compat",this.change,this)},render:function(){wp.media.view.Attachment.prototype.render.apply(this,arguments);var t=this.model.attributes.type;return"image"===t&&(d(this.$el),r(this.model)),this},change:function(){"image"===this.model.attributes.type&&r(this.model)}})}))},function(t,i,n){}],[[0,1]]]);
//# sourceMappingURL=admin-75f7df2c.js.map