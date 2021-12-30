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
(window.wpackioresponsivePicsfocalpointJsonp=window.wpackioresponsivePicsfocalpointJsonp||[]).push([[0],[function(t,i,e){e(1),e(2),t.exports=e(3)},function(t,i,e){var n="responsivePicsdist".replace(/[^a-zA-Z0-9_-]/g,"");e.p=window["__wpackIo".concat(n)]},function(t,i){var e;(e=jQuery)(document).ready((function(){var t,i,n,a,o={width:0,height:0},c=function(t){i.addClass("is-dragging")},p=function(t){i.removeClass("is-dragging")},d=function(t){t.preventDefault(),t.originalEvent.dataTransfer.dropEffect="none"},s=function(t){t.preventDefault(),console.log("dropFocalPoint",a.position())},r=function(e){var o=wp.media.template("attachment-select-focal-point"),c=e.find(".thumbnail"),p=e.find(".details-image");o&&(c.prepend(o),i=e.find(".image-focal"),n=e.find(".image-focal__wrapper"),a=e.find(".image-focal__point"),e.find(".image-focal__clickarea"),p.prependTo(n),t=n.find(".details-image"));var d=wp.media.template("attachment-save-focal-point"),s=e.find(".attachment-actions");d&&s.append(d)},l=function(i){var r,l,f=i.get("compat");if(f.item){var m=e(f.item).find(".compat-field-responsive_pics_focal_point_x input").val(),h=e(f.item).find(".compat-field-responsive_pics_focal_point_y input").val();r=m,l=h,t.on("load",(function(t){o={width:e(t.currentTarget).width(),height:e(t.currentTarget).height()},n.css({width:"".concat(o.width,"px"),height:"".concat(o.height,"px")})})),a.css({left:"".concat(r,"%"),top:"".concat(l,"%"),display:"block"}),n.on("dragover",d),n.on("drop",s),a.on("dragstart",c),a.on("dragend",p)}},f=wp.media.view.Attachment.Details.TwoColumn;wp.media.view.Attachment.Details.TwoColumn=f.extend({initialize:function(){this.model.on("change:compat",this.change,this)},render:function(){wp.media.view.Attachment.prototype.render.apply(this,arguments);var t=this.model.attributes.type;return"image"===t&&(r(this.$el),l(this.model)),this},change:function(){"image"===this.model.attributes.type&&l(this.model)}})}))},function(t,i,e){}],[[0,1]]]);
//# sourceMappingURL=admin-10abd63d.js.map