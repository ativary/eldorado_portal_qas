const exibeAlerta = function(tipo, msg, time, url){
	
	var time = time || false; //IE
	var url = url || false; //IE
    
    if(!time){
		Swal.fire({
			icon: tipo,
			html: msg
		})
	}else{

		if(url){
			Swal.fire({
				icon: tipo,
				html: msg,
				timer: parseInt(time) * 1000,
				timerProgressBar: true,
				showCancelButton: false,
				showCloseButton: false,
				allowOutsideClick: false,
				showConfirmButton: false,
				willClose: function(){
					window.location=url;
				}
			});
		}else{
			Swal.fire({
				icon: tipo,
				html: msg,
				timer: parseInt(time) * 1000,
				timerProgressBar: true,
				showCancelButton: false,
				showCloseButton: false,
				allowOutsideClick: false,
				showConfirmButton: false
			});
		}
		

	}

	document.querySelector('.swal2-confirm').addEventListener('click', function(event){
		openLoading(true);
	});
	document.querySelector('.swal2-container').addEventListener('mousemove', function(event){
		openLoading(true);
	});
	
}

const openLoading = function(close){
	var close = close || false;
    if(close){
        $("#modal_loading").modal('hide');
    }else{
        $("#modal_loading").modal({backdrop: 'static', keyboard: false, effect: 'blur'});
    }
}

var isSelect2 = $(".select2").length;
if(isSelect2 > 0){
	$(".select2").select2({
		width: '100%',
		language: {
			noResults: function(){
				return 'Nenhum resultado encontrado';
			}
		}
	});
}
