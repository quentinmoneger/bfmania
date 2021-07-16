/*==============================================================================
Init
==============================================================================*/
r.Explosion = function( opt ) {
	for( var k in opt ) {
		this[k] = opt[k];
	}
	this.tick = 0;
	this.tickMax = 20;
	if( r.slow ) {
		r.audio.play( 'explosionAlt' );
	} else {
		r.audio.play( 'explosion' );
	}
};

/*==============================================================================
Update
==============================================================================*/
r.Explosion.prototype.update = function( i ) {
	if( this.tick >= this.tickMax ) {
		r.explosions.splice( i, 1 );
	} else {
		this.tick += r.dt;
	}
};

/*==============================================================================
Render
==============================================================================*/
r.Explosion.prototype.render = function( i ) {
	if( r.util.arcInRect( this.x, this.y, this.radius, -r.screen.x, -r.screen.y, r.cw, r.ch ) ) {
		var radius = 1 + ( this.tick / ( this.tickMax / 2 ) ) * this.radius,
			lineWidth = r.util.rand( 1, this.radius / 2 );
		r.util.strokeCircle( r.ctxmg, this.x, this.y, radius, 'hsla(' + this.hue + ', ' + this.saturation + '%, ' + r.util.rand( 40, 80 ) + '%, ' + Math.min( 1, Math.max( 0, ( 1 - ( this.tick / this.tickMax ) ) ) ) + ')', lineWidth);
		r.ctxmg.beginPath();
		var size = r.util.rand( 1, 1.5 );
		for( var i = 0; i < 20; i++ ) {
			var angle = r.util.rand( 0, r.twopi ),
				x = this.x + Math.cos( angle ) * radius,
				y = this.y + Math.sin( angle ) * radius;
				
			r.ctxmg.rect( x - size / 2, y - size / 2, size, size );
		}
		r.ctxmg.fillStyle = 'hsla(' + this.hue + ', ' + this.saturation + '%, ' + r.util.rand( 50, 100 ) + '%, 1)';
		r.ctxmg.fill();

		r.ctxmg.fillStyle = 'hsla(' + this.hue + ', ' + this.saturation + '%, 50%, ' + Math.min( 1, Math.max( 0, ( 0.03 - ( this.tick / this.tickMax ) * 0.03 ) ) ) + ')';
		r.ctxmg.fillRect( -r.screen.x, -r.screen.y, r.cw, r.ch );
	}
};