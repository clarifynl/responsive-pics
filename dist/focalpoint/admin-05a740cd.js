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
(window.wpackioresponsivePicsfocalpointJsonp=window.wpackioresponsivePicsfocalpointJsonp||[]).push([[0],[function(i,t,e){e(1),e(2),i.exports=e(3)},function(i,t,e){var n="responsivePicsdist".replace(/[^a-zA-Z0-9_-]/g,"");e.p=window["__wpackIo".concat(n)]},function(i,t){var e;(e=jQuery)(document).ready((function(){var i,t,n,o,a=function(i){t.addClass("is-dragging")},c=function(i){console.log("draggingFocalPoint",i.target)},s=function(i){t.removeClass("is-dragging")},p=function(i){i.stopPropagation(),i.preventDefault()},d=function(i){i.stopPropagation(),i.preventDefault(),console.log("dropFocalPoint",o.position())},l=function(e){var a=wp.media.template("attachment-select-focal-point"),c=e.find(".thumbnail"),s=e.find(".details-image");a&&(c.prepend(a),t=e.find(".image-focal"),n=e.find(".image-focal__wrapper"),o=e.find(".image-focal__point"),e.find(".image-focal__clickarea"),s.prependTo(n),i=n.find(".details-image"),console.log(i.width(),i.height()));var p=wp.media.template("attachment-save-focal-point"),d=e.find(".attachment-actions");p&&d.append(p)},r=function(t){var l,r,f=t.get("compat");if(f.item){var g=e(f.item).find(".compat-field-responsive_pics_focal_point_x input").val(),m=e(f.item).find(".compat-field-responsive_pics_focal_point_y input").val();l=g,r=m,console.log(i,i.width(),i.css("width"),i.height(),i.css("height")),n.css({width:i.css("width"),height:i.css("height")}),o.css({left:"".concat(l,"%"),top:"".concat(r,"%"),display:"block"}),n.on("dragover",p),n.on("drop",d),o.on("dragstart",a),o.on("drag",c),o.on("dragend",s)}},f=wp.media.view.Attachment.Details.TwoColumn;wp.media.view.Attachment.Details.TwoColumn=f.extend({initialize:function(){this.model.on("change:compat",this.change,this)},render:function(){wp.media.view.Attachment.prototype.render.apply(this,arguments);var i=this.model.attributes.type;return"image"===i&&(l(this.$el),r(this.model)),this},change:function(){"image"===this.model.attributes.type&&r(this.model)}})}))},function(i,t,e){}],[[0,1]]]);
//# sourceMappingURL=admin-05a740cd.js.map