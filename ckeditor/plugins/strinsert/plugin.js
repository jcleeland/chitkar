/**
 * @license Copyright © 2013 Stuart Sillitoe <stuart@vericode.co.uk>
 * This work is mine, and yours. You can modify it as you wish.
 *
 * Stuart Sillitoe
 * stuartsillitoe.co.uk
 *
 */

CKEDITOR.plugins.add('strinsert',
{
	requires : ['richcombo'],
	init : function( editor )
	{
		//  array of strings to choose from that'll be inserted into the editor
		var strings = [];
		strings.push(['<p>If you are aware of any colleague who is not yet a member of CPSU, this is a great opportunity to speak with your colleagues about the importance and <a href="https://www.cpsuvic.org/benefit/">benefits of union membership</a> and encourage them to <a href="http://www.cpsuvic.org/member/">join CPSU</a> today.</p>', 'Join the union spiel', 'Join the union spiel']);
		strings.push(['<a href="https://www.cpsuvic.org/rewards/">benefits of union membership</a>','Benefits of union membership', 'Benefits of union membership']);
		strings.push(['<a href="http://www.cpsuvic.org/join/">join CPSU</a>', 'Join CPSU', 'Join CPSU']);
		strings.push(['<h3><strong>TOGETHER WE DO BETTER!</strong></h3><p><a href="http://www.cpsuvic.org/public_docs/What-has-CPSU-ever-done-for-us.pdf">http://www.cpsuvic.org/public_docs/What-has-CPSU-ever-done-for-us.pdf</a></p><p><a href="http://www.cpsuvic.org/public_docs/memform.pdf">http://www.cpsuvic.org/public_docs/memform.pdf</a></p>', 'Together We Do Better Spiel', 'Together We Do Better Spiel']);
		strings.push(['<p><strong>WAYNE TOWNSEND</strong><br />CPSU Victorian Assistant Branch Secretary</p>', 'Mitch Vandewerdt-Holman', 'Mitch Vandewerdt-Holman']);
		strings.push(['<p><strong>KAREN BATT</strong><br />CPSU Victorian Branch Secretary</p>', 'Jiselle Hanna', 'Jiselle Hanna']);
        /* strings.push(['@@CareerProfile::displayList()@@', 'Career Profiles', 'Career Profiles']);  */

		// add the menu to the editor
		editor.ui.addRichCombo('strinsert',
		{
			label: 		'Content',
			title: 		'Content',
			voiceLabel: 'Content',
			className: 	'cke_format',
			multiSelect:false,
			panel:
			{
				css: [ editor.config.contentsCss, CKEDITOR.skin.getPath('editor') ],
				voiceLabel: editor.lang.panelVoiceLabel
			},

			init: function()
			{
				this.startGroup( "Insert Content" );
				for (var i in strings)
				{
					this.add(strings[i][0], strings[i][1], strings[i][2]);
				}
			},

			onClick: function( value )
			{
				editor.focus();
				editor.fire( 'saveSnapshot' );
				editor.insertHtml(value);
				editor.fire( 'saveSnapshot' );
			}
		});
	}
});