function funcaoDeBusca() {
	var input, filter, table, tr, assunto, descricao, i, txtValue, txtValue2;
	input = document.getElementById("input");
	filter = input.value.toUpperCase();
	table = document.getElementById("minhaTabela");
	tr = table.getElementsByTagName("tr");

	for (i = 0; i < tr.length; i++) {
		assunto = tr[i].getElementsByTagName("td")[1];
		descricao = tr[i].getElementsByTagName("td")[5];
		if (assunto || descricao) {
			txtValue = assunto.textContent || assunto.innerText;
			txtValue2 = descricao.textContent || descricao.innerText;
			if (txtValue.toUpperCase().indexOf(filter) > -1 || txtValue2.toUpperCase().indexOf(filter) > -1) {
				tr[i].style.display = "";
			} else {
				tr[i].style.display = "none";
			}
		}
	}
}

function funcaoDeBuscaProtocolo() {
	var input, filter, table, tr, protocolo, i, txtValue;
	input = document.getElementById("inputProtocolo");
	filter = input.value.toUpperCase();
	table = document.getElementById("minhaTabela");
	tr = table.getElementsByTagName("tr");

	for (i = 0; i < tr.length; i++) {
		protocolo = tr[i].getElementsByTagName("td")[0];

		if (protocolo) {
			txtValue = protocolo.textContent || protocolo.innerText;

			if (txtValue.toUpperCase().indexOf(filter) > -1) {
				tr[i].style.display = "";
			} else {
				tr[i].style.display = "none";
			}
		}
	}
}