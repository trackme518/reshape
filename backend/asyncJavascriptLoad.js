//fetchTags();

function fetchTags(){
try {
		var text_data = downloadFile();        
        var _tags = text_data.split(",");
        var tagParent = document.getElementById("mytags");
        var newTags = "";
        
        for (var i = 0; i < _tags.length; i++) {
           newTags += '<input type="checkbox" id="tag'+i+'" name="tag'+i+'" value="'+_tags[i]+'">';
           newTags += '<label for="tag'+i+'">'+_tags[i].toUpperCase()+'</label>';
        }
       tagParent.innerHTML = newTags; 
	}
	catch(e) {
		alert(e.message);
	}
} 

async function fetchTags(){
try {
		var text_data = await downloadFile();    
        var _tags = text_data.split(",");
        var tagParent = document.getElementById("mytags");
        var newTags = "";
        
        for (var i = 0; i < _tags.length; i++) {
           newTags += '<input type="checkbox" id="tag'+i+'" name="tag'+i+'" value="'+_tags[i]+'">';
           newTags += '<label for="tag'+i+'">&nbsp;'+_tags[i].toUpperCase()+'&nbsp;</label>';
        }
       tagParent.innerHTML = newTags;        

	}
	catch(e) {
		alert(e.message);
	}
}

async function downloadFile() {
	let response = await fetch("tags.txt");		
	if(response.status != 200) {
		throw new Error("Server Error");
	}		
	// read response stream as text
	let text_data = await response.text();
	return text_data;
} 