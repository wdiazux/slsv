[SLSV Theme](http://leprosys.github.com/slsv/)
==========
Es un tema de Drupal para  http://www.slsv.org utilizando [Twitter Bootstrap](http://twitter.github.com/bootstrap/), [Susy](http://susy.oddbird.net/) y [Zen](http://drupal.org/project/zen). A pesar de que en el tema se hace uso de codigo de las herramientas anteriormente mensionadas no son depenendencias para poder ser utilizado.

El estado actual es en desarrollo, aún no esta implementado en el sitio.

Instalacion
-----------
```bash
$ git clone git://github.com/leprosys/slsv.git
```
Colocar la carpeta slsv en `sites/all/themes/`

Dependencias para desarrollo
----------------------------

Las siguientes aplicaciones solo son necesarias para poder modificar las hojas de estilo con sass:

* [rubygems](http://rubygems.org/) -- `apt-get install rubygems`
* [bootstrap-sass](https://github.com/thomas-mcdonald/bootstrap-sass) -- `gem install bootstrap-sass`
* [susy](http://susy.oddbird.net/) -- `gem install susy`


Contribuir
------------

1. Haz un Fork.
2. Crear un branch (`git checkout -b mi_version`)
3. Comitear tus cambios (`git commit -am "Añadida una opción"`)
4. Push el branch (`git push origin mi_version`)
5. Abrir un [Pull Request][1]
6. Esperar a una respuesta


[1]: http://github.com/leprosys/slsv/pulls
