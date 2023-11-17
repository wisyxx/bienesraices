<?php

declare(strict_types=1);

// DB
require '../../includes/app.php';
$db = conectarDB();

use App\Propiedad;

$consultaVendedores = "SELECT * FROM vendedores";
$resultadoVendedores = mysqli_query($db, $consultaVendedores);

$errores = Propiedad::getErrores();


$titulo = '';
$habitaciones = '';
$garages = '';
$wc = '';
$descripcion = '';
$precio = '';
$vendedorId = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $propiedad = new Propiedad($_POST);

    $errores = $propiedad->validar();
    if (!isset($errores)) {
        $propiedad->guardar();
    }

    $imagen = $_FILES['imagen'];


    /* TAMAÑO MAX IMAGEN */
    $medida = 1000 * 1000;

    if ($imagen['size'] > $medida) {
        $errores[] = "La imagen es muy pesada";
    }
    
    if (empty($errores)) {
        /** SUBIDA DE ARCHIVOS **/
        $carpetaImagenes = "../../imagenes/";
        if (!is_dir($carpetaImagenes)) {
            mkdir($carpetaImagenes);
        }

        // Crear nombres únicos
        $nombreImagen = md5(uniqid(strval(rand(1, 100)), true)) . '.jpg';

        move_uploaded_file($imagen["tmp_name"], $carpetaImagenes . $nombreImagen);

        $resultado = mysqli_query($db, $query);
    }

    if ($resultado) {
        header('Location: /admin?resultado=1');
    }
}

incluirTemplate('header');
?>

<!-- HTML -->
<main class="contenedor">
    <h1>Crear propiedad</h1>
    <a href="/admin" class="boton boton-verde">Volver</a>

    <?php foreach ($errores as $error) : ?>
        <div class="alerta error">
            <?php echo $error ?>
        </div>
    <?php endforeach; ?>

    <form class="formulario" method="post" action="/admin/propiedades/crear.php" enctype="multipart/form-data">
        <fieldset>
            <legend class="legend">Información general</legend>

            <label for="titulo">Titulo:</label>
            <input maxlength="45" type="text" name="titulo" id="titulo" placeholder="Tiulo propiedad" value="<?php echo $propiedad->titulo ?>">

            <label for="precio">Precio:</label>
            <input type="number" name="precio" id="precio" placeholder="Precio propiedad" value="<?php echo $propiedad->precio ?>">

            <label for="imagen">Imagen:</label>
            <input type="file" accept="image/jpeg, image/png" name="imagen" id="imagen">

            <label for="descripcion">Descripción</label>
            <textarea name="descripcion" id="descripcion" cols="30" rows="10"><?php echo $propiedad->descripcion ?></textarea>
        </fieldset>

        <fieldset>
            <legend class="legend">Información Propiedad</legend>

            <label for="habitaciones">Habitaciones:</label>
            <input type="number" name="habitaciones" id="habitaciones" placeholder="Ej: 3" value="<?php echo $propiedad->habitaciones ?>">

            <label for="baños">Baños:</label>
            <input type="number" name="wc" id="wc" placeholder="Ej: 3" value="<?php echo $propiedad->wc ?>">

            <label for="garages">Garages:</label>
            <input type="number" name="garages" id="garages" placeholder="Ej: 3" value="<?php echo $propiedad->garages ?>">
        </fieldset>
        <fieldset>
            <legend class="legend">Vendedor</legend>

            <select name="vendedorId" id="vendedor">
                <option value="">--Seleccione--</option>
                <?php while ($vendedor = mysqli_fetch_assoc($resultadoVendedores)) : ?>
                    <option <?php echo $vendedorId === $vendedor['id'] ? 'selected' : ''; ?> value="<?php echo $vendedor['id'] ?>"><?php echo $vendedor['nombre'] . ' ' . $vendedor['apellidos']; ?></option>
                <?php endwhile; ?>
            </select>
        </fieldset>

        <input type="submit" value="Crear Propiedad" class="boton boton-verde">
    </form>
</main>

<?php
incluirTemplate('footer');
?>