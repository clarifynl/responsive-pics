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
(window.wpackioresponsivePicsfocalpointJsonp=window.wpackioresponsivePicsfocalpointJsonp||[]).push([[0],[function(t,i,n){n(1),n(2),t.exports=n(3)},function(t,i,n){var e="responsivePicsdist".replace(/[^a-zA-Z0-9_-]/g,"");n.p=window["__wpackIo".concat(e)]},function(t,i){var n;(n=jQuery)(document).ready((function(){var t,i,e,o,a={width:0,height:0},c=function(t){i.addClass("is-dragging")},p=function(t){console.log("draggingFocalPoint",t.target)},s=function(t){i.removeClass("is-dragging")},d=function(t){t.stopPropagation(),t.preventDefault()},r=function(t){t.stopPropagation(),t.preventDefault(),console.log("dropFocalPoint",o.position())},l=function(n){var a=wp.media.template("attachment-select-focal-point"),c=n.find(".thumbnail"),p=n.find(".details-image");a&&(c.prepend(a),i=n.find(".image-focal"),e=n.find(".image-focal__wrapper"),o=n.find(".image-focal__point"),n.find(".image-focal__clickarea"),p.prependTo(e),t=e.find(".details-image"));var s=wp.media.template("attachment-save-focal-point"),d=n.find(".attachment-actions");s&&d.append(s)},f=function(i){var l,f,g=i.get("compat");if(g.item){var m=n(g.item).find(".compat-field-responsive_pics_focal_point_x input").val(),h=n(g.item).find(".compat-field-responsive_pics_focal_point_y input").val();l=m,f=h,t.on("load",(function(t){a={width:n(t.currentTarget).width(),height:n(t.currentTarget).height()}})),e.css({width:a.width,height:a.height}),o.css({left:"".concat(l,"%"),top:"".concat(f,"%"),display:"block"}),e.on("dragover",d),e.on("drop",r),o.on("dragstart",c),o.on("drag",p),o.on("dragend",s)}},g=wp.media.view.Attachment.Details.TwoColumn;wp.media.view.Attachment.Details.TwoColumn=g.extend({initialize:function(){this.model.on("change:compat",this.change,this)},render:function(){wp.media.view.Attachment.prototype.render.apply(this,arguments);var t=this.model.attributes.type;return"image"===t&&(l(this.$el),f(this.model)),this},change:function(){"image"===this.model.attributes.type&&f(this.model)}})}))},function(t,i,n){}],[[0,1]]]);
//# sourceMappingURL=admin-a9a9bc7c.js.map