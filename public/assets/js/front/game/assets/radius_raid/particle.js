/*==============================================================================
Init
==============================================================================*/
r.Particle = function( opt ) {
	for( var k in opt ) {
		this[k] = opt[k];
	}
};

/*==============================================================================
Update
==============================================================================*/
r.Particle.prototype.update = function( i ) {
	/*==============================================================================
	Apply Forces
	==============================================================================*/
	this.x += Math.cos( this.direction ) * ( this.speed * r.dt );
	this.y += Math.sin( this.direction ) * ( this.speed * r.dt );
	this.ex = this.x - Math.cos( this.direction ) * this.speed;
	this.ey = this.y - Math.sin( this.direction ) * this.speed;
	this.speed *= this.friction;

	/*==============================================================================
	Lock Bounds
	==============================================================================*/
	if( !r.util.pointInRect( this.ex, this.ey, 0, 0, r.ww, r.wh ) || this.speed <= 0.05 ) {
		this.parent.splice( i, 1 );
	}

	/*==============================================================================
	Update View
	==============================================================================*/
	if( r.util.pointInRect( this.ex, this.ey, -r.screen.x, -r.screen.y, r.cw, r.ch ) ) {
		this.inView = 1;
	} else {
		this.inView = 0;
	}
};

/*==============================================================================
Render
==============================================================================*/
r.Particle.prototype.render = function( i ) {
	if( this.inView ) {
		r.ctxmg.beginPath();
		r.ctxmg.moveTo( this.x, this.y );
		r.ctxmg.lineTo( this.ex, this.ey );
		r.ctxmg.lineWidth = this.lineWidth;
		r.ctxmg.strokeStyle = 'hsla(' + this.hue + ', ' + this.saturation + '%, ' + r.util.rand( 50, 100 ) + '%, 1)';
		r.ctxmg.stroke();
	}
}