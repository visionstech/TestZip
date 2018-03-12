function addLoader(id){
	$(id).waitMe({
	effect : 'win8', 
	text : '', 
	bg : 'rgba(255,255,255,0.7)', 
	color : '#000', 
	sizeW : '20px', 
	sizeH : '20px', 
	source : ''
	});
}

function removeLoader(id){
	$(id).waitMe("hide");
}
