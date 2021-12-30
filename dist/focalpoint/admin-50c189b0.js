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
(window.wpackioresponsivePicsfocalpointJsonp=window.wpackioresponsivePicsfocalpointJsonp||[]).push([[0],[function(t,i,n){n(1),n(2),t.exports=n(3)},function(t,i,n){var e="responsivePicsdist".replace(/[^a-zA-Z0-9_-]/g,"");n.p=window["__wpackIo".concat(e)]},function(t,i){var n;(n=jQuery)(document).ready((function(){var t,i,e,a,o={width:0,height:0},c=function(t){i.addClass("is-dragging"),t.originalEvent.dataTransfer.effectAllowed="move"},p=function(t){console.log("draggingFocalPoint")},r=function(t){i.removeClass("is-dragging")},d=function(t){t.originalEvent.stopPropagation(),t.originalEvent.preventDefault(),t.originalEvent.dataTransfer.dropEffect="move"},s=function(t){t.originalEvent.stopPropagation(),t.originalEvent.preventDefault(),console.log("dropFocalPoint",a.position())},l=function(n){var o=wp.media.template("attachment-select-focal-point"),c=n.find(".thumbnail"),p=n.find(".details-image");o&&(c.prepend(o),i=n.find(".image-focal"),e=n.find(".image-focal__wrapper"),a=n.find(".image-focal__point"),n.find(".image-focal__clickarea"),p.prependTo(e),t=e.find(".details-image"));var r=wp.media.template("attachment-save-focal-point"),d=n.find(".attachment-actions");r&&d.append(r)},f=function(i){var l,f,g=i.get("compat");if(g.item){var m=n(g.item).find(".compat-field-responsive_pics_focal_point_x input").val(),h=n(g.item).find(".compat-field-responsive_pics_focal_point_y input").val();l=m,f=h,t.on("load",(function(t){o={width:n(t.currentTarget).width(),height:n(t.currentTarget).height()},e.css({width:"".concat(o.width,"px"),height:"".concat(o.height,"px")})})),a.css({left:"".concat(l,"%"),top:"".concat(f,"%"),display:"block"}),e.on("dragover",d),e.on("drop",s),a.on("dragstart",c),a.on("drag",p),a.on("dragend",r)}},g=wp.media.view.Attachment.Details.TwoColumn;wp.media.view.Attachment.Details.TwoColumn=g.extend({initialize:function(){this.model.on("change:compat",this.change,this)},render:function(){wp.media.view.Attachment.prototype.render.apply(this,arguments);var t=this.model.attributes.type;return"image"===t&&(l(this.$el),f(this.model)),this},change:function(){"image"===this.model.attributes.type&&f(this.model)}})}))},function(t,i,n){}],[[0,1]]]);
//# sourceMappingURL=admin-50c189b0.js.map