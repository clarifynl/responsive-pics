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
(window.wpackioresponsivePicsfocalpointJsonp=window.wpackioresponsivePicsfocalpointJsonp||[]).push([[0],[function(t,n,i){i(1),i(2),t.exports=i(3)},function(t,n,i){var e="responsivePicsdist".replace(/[^a-zA-Z0-9_-]/g,"");i.p=window["__wpackIo".concat(e)]},function(t,n){var i;(i=jQuery)(document).ready((function(){var t,n,e,o,a=function(t){n.addClass("is-dragging")},c=function(t){console.log("draggingFocalPoint",t.target)},p=function(t){n.removeClass("is-dragging")},r=function(t){t.stopPropagation(),t.preventDefault()},s=function(t){t.stopPropagation(),t.preventDefault(),console.log("dropFocalPoint",o.position())},l=function(a){var c=wp.media.template("attachment-select-focal-point"),p=a.find(".thumbnail"),r=a.find(".details-image");r.on("load",(function(t){console.log(i(t.currentTarget).width(),i(t.currentTarget).height())})),c&&(p.prepend(c),n=a.find(".image-focal"),e=a.find(".image-focal__wrapper"),o=a.find(".image-focal__point"),a.find(".image-focal__clickarea"),r.prependTo(e),t=e.find(".details-image"));var s=wp.media.template("attachment-save-focal-point"),l=a.find(".attachment-actions");s&&l.append(s)},d=function(n){var l,d,f=n.get("compat");if(f.item){var g=i(f.item).find(".compat-field-responsive_pics_focal_point_x input").val(),m=i(f.item).find(".compat-field-responsive_pics_focal_point_y input").val();l=g,d=m,console.log(t,t.width(),t.height()),t.on("load",(function(t){console.log(i(t.currentTarget).width(),i(t.currentTarget).height())})),o.css({left:"".concat(l,"%"),top:"".concat(d,"%"),display:"block"}),e.on("dragover",r),e.on("drop",s),o.on("dragstart",a),o.on("drag",c),o.on("dragend",p)}},f=wp.media.view.Attachment.Details.TwoColumn;wp.media.view.Attachment.Details.TwoColumn=f.extend({initialize:function(){this.model.on("change:compat",this.change,this)},render:function(){wp.media.view.Attachment.prototype.render.apply(this,arguments);var t=this.model.attributes.type;return"image"===t&&(l(this.$el),d(this.model)),this},change:function(){"image"===this.model.attributes.type&&d(this.model)}})}))},function(t,n,i){}],[[0,1]]]);
//# sourceMappingURL=admin-ef151651.js.map