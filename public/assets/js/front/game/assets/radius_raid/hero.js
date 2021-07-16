/*==============================================================================
Init
==============================================================================*/
r.Hero = function() {
	this.x = r.ww / 2;
	this.y = r.wh / 2;
	this.vx = 0;
	this.vy = 0;
	this.vmax = 4;
	this.vmax = 6;
	this.direction = 0;
	this.accel = 0.5;
	this.radius = 10;
	this.life = 1;
	this.takingDamage = 0;
	this.fillStyle = '#fff';
	this.weapon = {
		fireRate: 5,
		fireRateTick: 5,
		spread: 0.3,
		count: 1,
		bullet: {
			size: 15,
			lineWidth: 2,
			damage: 1,
			speed: 10,
			piercing: 0,
			strokeStyle: '#fff'
		},
		fireFlag: 0
	};	
};

/*==============================================================================
Update
==============================================================================*/
r.Hero.prototype.update = function() {
	if( this.life > 0 ) {
		/*==============================================================================
		Apply Forces
		==============================================================================*/
		if( r.keys.state.up ) {
			this.vy -= this.accel * r.dt;
			if( this.vy < -this.vmax ) {
				this.vy = -this.vmax;
			}
		} else if( r.keys.state.down ) {
			this.vy += this.accel * r.dt;
			if( this.vy > this.vmax ) {
				this.vy = this.vmax;
			}
		}
		if( r.keys.state.left ) {
			this.vx -= this.accel * r.dt;
			if( this.vx < -this.vmax ) {
				this.vx = -this.vmax;
			}
		} else if( r.keys.state.right ) {
			this.vx += this.accel * r.dt;
			if( this.vx > this.vmax ) {
				this.vx = this.vmax;
			}
		}

		this.vy *= 0.9;
		this.vx *= 0.9;	
		
		this.x += this.vx * r.dt;
		this.y += this.vy * r.dt;

		/*==============================================================================
		Lock Bounds
		==============================================================================*/
		if( this.x >= r.ww - this.radius ) {
			this.x = r.ww - this.radius;
		}
		if( this.x <= this.radius ) {
			this.x = this.radius;
		}
		if( this.y >= r.wh - this.radius ) {
			this.y = r.wh - this.radius;
		}
		if( this.y <= this.radius ) {
			this.y = this.radius;
		}

		/*==============================================================================
		Update Direction
		==============================================================================*/
		var dx = r.mouse.x - this.x,
			dy = r.mouse.y - this.y;
		this.direction = Math.atan2( dy, dx );

		/*==============================================================================
		Fire Weapon
		==============================================================================*/
		if( this.weapon.fireRateTick < this.weapon.fireRate ){
			this.weapon.fireRateTick += r.dt;
		} else {
			if( r.autofire || ( !r.autofire && r.mouse.down ) ){
				r.audio.play( 'shoot' );
				if( r.powerupTimers[ 2 ] > 0 || r.powerupTimers[ 3 ] > 0 || r.powerupTimers[ 4 ] > 0) {
					r.audio.play( 'shootAlt' );
				}

				this.weapon.fireRateTick = this.weapon.fireRateTick - this.weapon.fireRate;
				this.weapon.fireFlag = 6;

				if( this.weapon.count > 1 ) {
					var spreadStart = -this.weapon.spread / 2;
					var spreadStep = this.weapon.spread / ( this.weapon.count - 1 );
				} else {
					var spreadStart = 0;
					var spreadStep = 0;
				}

				var gunX = this.x + Math.cos( this.direction ) * ( this.radius + this.weapon.bullet.size );
				var gunY = this.y + Math.sin( this.direction ) * ( this.radius + this.weapon.bullet.size );

				for( var i = 0; i < this.weapon.count; i++ ) {
					r.bulletsFired++;
					var color = this.weapon.bullet.strokeStyle;
					if( r.powerupTimers[ 2 ] > 0 || r.powerupTimers[ 3 ] > 0 || r.powerupTimers[ 4 ] > 0) {
						var colors = [];
						if( r.powerupTimers[ 2 ] > 0 ) { colors.push( 'hsl(' + r.definitions.powerups[ 2 ].hue + ', ' + r.definitions.powerups[ 2 ].saturation + '%, ' + r.definitions.powerups[ 2 ].lightness + '%)' ); }
						if( r.powerupTimers[ 3 ] > 0 ) { colors.push( 'hsl(' + r.definitions.powerups[ 3 ].hue + ', ' + r.definitions.powerups[ 3 ].saturation + '%, ' + r.definitions.powerups[ 3 ].lightness + '%)' ); }
						if( r.powerupTimers[ 4 ] > 0 ) { colors.push( 'hsl(' + r.definitions.powerups[ 4 ].hue + ', ' + r.definitions.powerups[ 4 ].saturation + '%, ' + r.definitions.powerups[ 4 ].lightness + '%)' ); }
						color = colors[ Math.floor( r.util.rand( 0, colors.length ) ) ];
					}
					r.bullets.push( new r.Bullet( {					
						x: gunX,
						y: gunY,
						speed: this.weapon.bullet.speed,
						direction: this.direction + spreadStart + i * spreadStep,
						damage: this.weapon.bullet.damage,
						size: this.weapon.bullet.size,
						lineWidth: this.weapon.bullet.lineWidth,
						strokeStyle: color,
						piercing: this.weapon.bullet.piercing					
					} ) );
				}
			}
		}

		/*==============================================================================
		Check Collisions
		==============================================================================*/
		this.takingDamage = 0;
		var ei = r.enemies.length;
		while( ei-- ) {
			var enemy = r.enemies[ ei ];
			if( enemy.inView && r.util.distance( this.x, this.y, enemy.x, enemy.y ) <= this.radius + enemy.radius ) {
				r.particleEmitters.push( new r.ParticleEmitter( {
					x: this.x,
					y: this.y,
					count: 2,
					spawnRange: 0,
					friction: 0.85,
					minSpeed: 2,
					maxSpeed: 15,
					minDirection: 0,
					maxDirection: r.twopi,
					hue: 0,
					saturation: 0
				} ) );
				this.takingDamage = 1;
				this.life -= 0.0075;
				r.rumble.level = 3;
				if( Math.floor( r.tick ) % 5 == 0 ){
					r.audio.play( 'takingDamage' );
				}
			}
		}		
	}
};

/*==============================================================================
Render
==============================================================================*/
r.Hero.prototype.render = function() {
	if( this.life > 0 ) {
		if( this.takingDamage ) {
			var fillStyle = 'hsla(0, 0%, ' + r.util.rand( 0, 100 ) + '%, 1)';
			r.ctxmg.fillStyle = 'hsla(0, 0%, ' + r.util.rand( 0, 100 ) + '%, ' + r.util.rand( 0.01, 0.15 ) + ')';
			r.ctxmg.fillRect( -r.screen.x, -r.screen.y, r.cw, r.ch );
		} else if( this.weapon.fireFlag > 0 ) {
			this.weapon.fireFlag -= r.dt;
			var fillStyle = 'hsla(' + r.util.rand( 0, 359 ) + ', 100%, ' + r.util.rand( 20, 80 ) + '%, 1)';
		} else {
			var fillStyle = this.fillStyle;
		}

		r.ctxmg.save();
		r.ctxmg.translate( this.x, this.y );
		r.ctxmg.rotate( this.direction - r.pi / 4 );
		r.ctxmg.fillStyle = fillStyle;
		r.ctxmg.fillRect( 0, 0, this.radius, this.radius );
		r.ctxmg.restore();

		r.ctxmg.save();
		r.ctxmg.translate( this.x, this.y );	
		r.ctxmg.rotate( this.direction - r.pi / 4 + r.twopi / 3 );
		r.ctxmg.fillStyle = fillStyle;
		r.ctxmg.fillRect( 0, 0, this.radius, this.radius );
		r.ctxmg.restore();

		r.ctxmg.save();
		r.ctxmg.translate( this.x, this.y );	
		r.ctxmg.rotate( this.direction - r.pi / 4 - r.twopi / 3 );
		r.ctxmg.fillStyle = fillStyle;
		r.ctxmg.fillRect( 0, 0, this.radius, this.radius );
		r.ctxmg.restore();

		r.util.fillCircle( r.ctxmg, this.x, this.y, this.radius - 3, fillStyle );
	}	
};