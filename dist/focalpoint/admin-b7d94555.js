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
(window.wpackioresponsivePicsfocalpointJsonp=window.wpackioresponsivePicsfocalpointJsonp||[]).push([[0],[function(t,i,n){n(1),n(2),t.exports=n(3)},function(t,i,n){var e="responsivePicsdist".replace(/[^a-zA-Z0-9_-]/g,"");n.p=window["__wpackIo".concat(e)]},function(t,i){var n;(n=jQuery)(document).ready((function(){var t,i,e,o,a=function(t){i.addClass("is-dragging")},c=function(t){console.log("draggingFocalPoint",t.target)},s=function(t){i.removeClass("is-dragging")},p=function(t){t.stopPropagation(),t.preventDefault()},d=function(t){t.stopPropagation(),t.preventDefault(),console.log("dropFocalPoint",o.position())},l=function(n){var a=wp.media.template("attachment-select-focal-point"),c=n.find(".thumbnail");t=n.find(".details-image"),a&&(c.prepend(a),i=n.find(".image-focal"),e=n.find(".image-focal__wrapper"),o=n.find(".image-focal__point"),n.find(".image-focal__clickarea"),t.prependTo(e));var s=wp.media.template("attachment-save-focal-point"),p=n.find(".attachment-actions");s&&p.append(s)},r=function(i){var l,r,f=i.get("compat");if(f.item){var m=n(f.item).find(".compat-field-responsive_pics_focal_point_x input").val(),g=n(f.item).find(".compat-field-responsive_pics_focal_point_y input").val();l=m,r=g,console.log(t.css("width"),t.css("height")),e.css({width:t.css("width"),height:t.css("height")}),o.css({left:"".concat(l,"%"),top:"".concat(r,"%"),display:"block"}),e.on("dragover",p),e.on("drop",d),o.on("dragstart",a),o.on("drag",c),o.on("dragend",s)}},f=wp.media.view.Attachment.Details.TwoColumn;wp.media.view.Attachment.Details.TwoColumn=f.extend({initialize:function(){this.model.on("change:compat",this.change,this)},render:function(){wp.media.view.Attachment.prototype.render.apply(this,arguments);var t=this.model.attributes.type;return"image"===t&&(l(this.$el),r(this.model)),this},change:function(){"image"===this.model.attributes.type&&r(this.model)}})}))},function(t,i,n){}],[[0,1]]]);
//# sourceMappingURL=admin-b7d94555.js.map