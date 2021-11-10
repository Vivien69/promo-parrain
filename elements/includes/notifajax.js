// AJax pour les notifications
function notifAjaxData(limit,startn,idmembre) {
	
	var data = new FormData();
	data.append("json", JSON.stringify({idmembre:idmembre, limit:limit, startn:startn}));

	whereContent = document.getElementById('notifload-content');
	whereContentMessage = document.getElementById('notifload-content-message');
	whereContentMessage.innerHTML = '<button type="button">Veuillez patienter ...</button>';
	
	fetch("/elements/includes/notifload-content.ajax.php", {
		method : "POST",
		body: data,
	})
	.then((response) => response.json())
	.catch((erreur) => {
		whereContentMessage.innerHTML = '<button type="button">Erreur, réesayer ...</button>';
	})
	.then((data) => {

		let append = '';
		for(let i = 0; i < data.length; i++) {
			
			let obj = data[i];

			
			switch(obj.action) {

				case '1': //action 1 : Click du lien d'une annonce
					append +=  '<li><a href="/membres/parrainages" class="notif-append '+(obj.vu == 0 ? 'notifNotOpen' : '')+'"><div><img style="width:70px;height:auto;" src="/membres/includes/uploads-img/120-'+(obj.image !== null ? obj.image : "default_avatar.png")+'" /></div><div class=""><p><b>'+obj.sendername+'</b> à cliquer sur votre lien '+obj.texte+'</p><i style="margin-right:7px;" class="fas fa-link fa-lg"></i><i>'+obj.date+'</i></div></a></li>';
				break;

				case '2': //action 2 : Copie du code d'une annonce
					append +=  '<li><a href="/membres/parrainages" class="notif-append '+(obj.vu == 0 ? 'notifNotOpen' : '')+'"><div><img style="width:70px;height:auto;" src="/membres/includes/uploads-img/120-'+(obj.image !== null ? obj.image : "default_avatar.png")+'" /></div><div class=""><p><b>'+obj.sendername+'</b> copié votre code '+obj.texte+'</p><i style="margin-right:7px;" class="fas fa-link fa-lg"></i><i>'+obj.date+'</i></div></a></li>';
				break;

				case '3': //action 3 : Nouveauté dans les parrainages idsender = id filleul ou parrain & annonce = lien
					
				break;

				case '4': //action 4 : Nouveau message
					
				break;

				case '5': //action 5 : Nouvel avis sur son profil
					append += '<li><a href="/membres/profil/'+obj.idmembre+'#comments" class="notif-append '+(obj.vu == 0 ? 'notifNotOpen' : '')+'"><div></div><div class=""><p><b>'+obj.sendername+'</b> vous à ajouter un avis sur votre profil</p><i style="margin-right:7px;" class="fas fa-link fa-lg"></i><i>'+obj.date+'</i></div></a></li>';
				break;

				case '6': //action 6 : Nouveau badge
					append +=  '<li><a href="/membres/badges" class="notif-append '+(obj.vu == 0 ? 'notifNotOpen' : '')+'"><div><i class="fas fa-comment fa-lg"></i></div><div class=""><p>Félicitations, vous venez d\'obtenir un nouveau badge '+obj.texte+'</p><i style="margin-right:7px;" class="fas fa-trophy fa-lg"></i><i>'+obj.date+'</i></div></a></li>';
				break;

				case '7': //action 7 : Refus points
					
				break;
				}
		}
		setTimeout(function() {
			whereContent.innerHTML += append;

			if(data == '') 
		{
			whereContentMessage.innerHTML = '<button type="button">Aucun resultat</button>';
			action = 'active';
		} 
		else 
		{
			whereContentMessage.innerHTML = '';
			action = 'inactive';
		}
		}, 500)

	})
}

// Ajax pour les notification de messagerie
function MessagerieAjaxData(limit,startm,idmembre) {
	
	var data = new FormData();
	data.append("json", JSON.stringify({idmembre:idmembre, limit:limit, startm:startm}));

	whereContent = document.getElementById('messagerie-content');
	whereContentMessage = document.getElementById('messagerie-content-message');
	whereContentMessage.innerHTML = '<button type="button">Veuillez patienter ...</button>';
	
	fetch("/elements/includes/messagerie-content.ajax.php", {
		method : "POST",
		body: data,
	})
	.then((response) => response.json())
	.catch((erreur) => {
		whereContentMessage.innerHTML = '<button type="button">Erreur, réesayer ...</button>'
	})
	.then((data) => {
		
        let append = '';
		for(let i = 0; i < data.length; i++) {
			
			let obj = data[i];

            (obj.id1 == idmembre ? lu = obj.lu2 :  lu = obj.lu1)
            append += '<li><a href="/membres/messagerie#'+obj.conversation_id+'" class="'+(lu == 0 ? 'notifNotOpen' : '')+' notif-append" style="'+(lu == 0 ? 'border-bottom : 1px solid #701818;' : 'border-bottom: 1px solid #F1F1F1;')+'"><div><img class="avatar" style="width:45px;height:45px;" src="/membres/images/'+(obj.mid1 == idmembre ? (obj.image2 !== null ? obj.image2 : "default_avatar.png") : (obj.image1 !== null ? obj.image1 : "default_avatar.png"))+'" /></div><div class=""><p>Vous avez un nouveau message de <b>'+(obj.mid1 == idmembre ? obj.nom2 : obj.nom1)+'</b></p><i>'+obj.date+'</i></div></a></li>';

        }
		setTimeout(function() {
            
			whereContent.innerHTML += append;


			if(data == '') 
		{
			whereContentMessage.innerHTML = '<button type="button">Aucun resultat</button>';
			action = 'active';
		} 
		else 
		{
			whereContentMessage.innerHTML = '';
			action = 'inactive';
		}
		}, 500)

	})
}