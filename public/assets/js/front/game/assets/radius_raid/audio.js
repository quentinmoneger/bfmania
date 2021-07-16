r.audio = {
	sounds: {},
	references: [],
	play: function( sound ) {
		if( !r.mute ){
			var audio = r.audio.sounds[ sound ];
			if( audio.length > 1 ){
				audio = r.audio.sounds[ sound ][ Math.floor( r.util.rand( 0, audio.length ) ) ];
			} else {
				audio = r.audio.sounds[ sound ][ 0 ];
			}
			audio.pool[ audio.tick ].play();		
			if( audio.tick < audio.count - 1 ) {
				audio.tick++;
			} else {
				audio.tick = 0;
			}
		}
	}
};

for( var k in r.definitions.audio ) {
	r.audio.sounds[ k ] = [];

	r.definitions.audio[ k ].params.forEach( function( elem, index, array ) {
		r.audio.sounds[ k ].push( {
			tick: 0,
			count: r.definitions.audio[ k ].count,
			pool: []
		} );

		for( var i = 0; i < r.definitions.audio[ k ].count; i++ ) {
			var audio = new Audio();
			audio.src = jsfxr( elem );			
			r.audio.references.push( audio );
			r.audio.sounds[ k ][ index ].pool.push( audio );
		}

	} );
}	

