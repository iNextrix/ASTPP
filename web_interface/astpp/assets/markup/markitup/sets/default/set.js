// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2011 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
// Html tags
// http://en.wikipedia.org/wiki/html
// ----------------------------------------------------------------------------
// Basic set. Feel free to add more tags
// ----------------------------------------------------------------------------
var mySettings = {
	onShiftEnter:  	{keepDefault:false, replaceWith:'<br />\n'},
	onCtrlEnter:  	{keepDefault:false, openWith:'\n<p>', closeWith:'</p>'},
	onTab:    		{keepDefault:false, replaceWith:'    '},
	markupSet:  [ 	
		{name:'Heading 1', key:'1', openWith:'<h1>', closeWith:'</h1>'  },
		{separator:'---------------' },
		{name:'Heading 2', key:'2', openWith:'<h2>', closeWith:'</h2>'   },
		{separator:'---------------' },
		{name:'Heading 3', key:'3', openWith:'<h3>', closeWith:'</h3>'  },
		{separator:'---------------' },
		{name:'Heading 4', key:'4', openWith:'<h4>', closeWith:'</h4>'   },
		{separator:'---------------' },
		{name:'Heading 5', key:'5', openWith:'<h5>', closeWith:'</h5>'   },
		{separator:'---------------' },
		{name:'Heading 6', key:'6', openWith:'<h6>', closeWith:'</h6>'   },
		{separator:'---------------' },
		{name:'Paragraph', key:'P', openWith:'<p>', closeWith:'</p>'  },
		{separator:'---------------' },
		{name:'Bold', key:'B', openWith:'(!(<strong>|!|<b>)!)', closeWith:'(!(</strong>|!|</b>)!)' },
		{separator:'---------------' },
		{name:'Italic', key:'I', openWith:'(!(<em>|!|<i>)!)', closeWith:'(!(</em>|!|</i>)!)'  },
		{separator:'---------------' },
		{name:'Stroke through', key:'S', openWith:'<del>', closeWith:'</del>' },
		{separator:'---------------' },
		{name:'Bulleted List', openWith:'    <li>', closeWith:'</li>', multiline:true, openBlockWith:'<ul>\n', closeBlockWith:'\n</ul>'},
		{separator:'---------------' },
		{name:'Numeric List', openWith:'    <li>', closeWith:'</li>', multiline:true, openBlockWith:'<ol>\n', closeBlockWith:'\n</ol>'},
		{separator:'---------------' },
		{name:'Picture', key:'P', replaceWith:'<img src="[![Source:!:http://]!]" alt="[![Alternative text]!]" />' },
		{separator:'---------------' },
		{name:'Link', key:'L', openWith:'<a href="[![Link:!:http://]!]"(!( title="[![Title]!]")!)>', closeWith:'</a>', placeHolder:'Your text to link...' },
		{separator:'---------------' },
		{name:'Clean', className:'clean', replaceWith:function(markitup) { return markitup.selection.replace(/<(.*?)>/g, "") } },		
		{separator:'---------------' },
		{name:'Preview', className:'preview',  call:'preview'}

	]
}
