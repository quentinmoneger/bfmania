/*==============================================================================
Init
==============================================================================*/
r.Bullet = function( opt ) {
	for( var k in opt ) {
		this[k] = opt[k];
	}
	this.enemiesHit = [];
	this.inView = 0;
	r.particleEmitters.push( new r.ParticleEmitter( {
		x: this.x,
		y: this.y,
		count: 1,
		spawnRange: 1,
		friction: 0.75,
		minSpeed: 2,
		maxSpeed: 10,
		minDirection: 0,
		maxDirection: r.twopi,
		hue: 0,
		saturation: 0
	} ) );
};

/*==============================================================================
Update
==============================================================================*/
r.Bullet.prototype.update = function( i ) {
	/*==============================================================================
	Apply Forces
	==============================================================================*/
	this.x += Math.cos( this.direction ) * ( this.speed * r.dt );
	this.y += Math.sin( this.direction ) * ( this.speed * r.dt );
	this.ex = this.x - Math.cos( this.direction ) * this.size;
	this.ey = this.y - Math.sin( this.direction ) * this.size;

	/*==============================================================================
	Check Collisions
	==============================================================================*/
	var ei = r.enemies.length;
	while( ei-- ) {
		var enemy = r.enemies[ ei ];
		if( r.util.distance( this.x, this.y, enemy.x, enemy.y ) <= enemy.radius ) {
			if( this.enemiesHit.indexOf( enemy.index ) == -1 ){
				r.particleEmitters.push( new r.ParticleEmitter( {
					x: this.x,
					y: this.y,
					count: Math.floor( r.util.rand( 1, 4 ) ),
					spawnRange: 0,
					friction: 0.85,
					minSpeed: 5,
					maxSpeed: 12,
					minDirection: ( this.direction - r.pi ) - r.pi / 5,
					maxDirection: ( this.direction - r.pi ) + r.pi / 5,
					hue: enemy.hue
				} ) );

				this.enemiesHit.push( enemy.index );
				enemy.receiveDamage( ei, this.damage );

				if( this.enemiesHit.length > 3 ) {
					r.bullets.splice( i, 1 );
				}						
			}
			if( !this.piercing ) {
				r.bullets.splice( i, 1 );
			}
		}
	}

	/*==============================================================================
	Lock Bounds
	==============================================================================*/
	if( !r.util.pointInRect( this.ex, this.ey, 0, 0, r.ww, r.wh ) ) {
		r.bullets.splice( i, 1 );
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
r.Bullet.prototype.render = function( i ) {
	if( this.inView ) {
		r.ctxmg.beginPath();
		r.ctxmg.moveTo( this.x, this.y );
		r.ctxmg.lineTo( this.ex, this.ey );
		r.ctxmg.lineWidth = this.lineWidth;		
		r.ctxmg.strokeStyle = this.strokeStyle;
		r.ctxmg.stroke();
	}
};