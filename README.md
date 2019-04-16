# Requisitos

- Composer
- NodeJs (stable version)
- [ After Nodejs installed ] Angular CLI
- Conexão com a Internet ( para as requisições no site do seminovosbh )

OBS: Para instalar o Angular Cli use o comando

```
$ npm install -g @angular/cli
```

# Subindo o Backend Server ( Laravel )

Abra um terminao e dentro do diretório seminovosbh use o comando

```
$ php artisan serve
```

Obs: Observe a porta em que o backend será servido. A porta padrão é 8000.
Caso esta porta já esteja em uso, outra porta será utilizada automaticamente,
e isso será mostrado no console assim que entrar com o comando acima.

Obs2: Nao feche o terminal.

# Subindo a Aplicação Frontend ( Angular )

Abra outra aba do terminal e dentro do diretório seminovosbh-angular use o comando

```
$ ng serve --open
```

Obs: A flag --open é utilizada pra abrir o browser já na url em que a aplicação
está rodando. Mas caso isso nao aconteça, copie a url no console e abra no navegador de preferência.
Obs2: Nao feche o terminal.

# Utilizando a Aplicação

Escolha a Marca e o Modelo ( O modelo nao é estritamente necessário para alguns casos ),
e clique em buscar para que a listagem de carros desejada apareça.
