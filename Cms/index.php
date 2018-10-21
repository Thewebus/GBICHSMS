<!DOCTYPE html>
<html>
<head>
    <title>Renseignement</title>
    <link rel="stylesheet" href="assets/indexStyle.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>
<body>
<div class="container">
    <h3> class="text-center" margin>Renseignement</h3>
    <form method="post" action="preview.php">
        <div class="form-group">
            <label> Contenu du sms</label>
            <textarea class="form-control" rows="5" id="sms" name="sms" required></textarea>
            <div class="help-block text-danger">Veuillez indiquer le sms à envoyer svp!</div>
        </div>
        <div class="text-right" id="ecran" style="font-weight: bold; font-size: 1.5em;">nombre de caractères
            restant:160
        </div>

        <div class="panel panel-default rounded">
            <div class="panel-heading">Diffusion</div>
            <div class="panel-body">
                <div class="form-group row">
                    <label class="col-sm-1 col-form-label">Date</label>
                    <div class="col-sm-5">
                        <input type="date" name="date_diff" class="form-control" required>
                        <div class="help-block text-danger">Veuillez renseigner la date de diffusion svp!</div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-1 col-form-label">Heure</label>
                    <div class="col-sm-5">
                        <input type="time" name="heure_diff" class="form-control" required>
                        <div class="help-block text-danger">Veuillez renseigner l'heure de diffusion svp!</div>
                    </div>
                </div>
            </div>

        </div>

        <button type="submit" class="btn btn-primary">Prévisualier</button>
    </form>
</div>

</body>
</html>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script type="text/javascript">
    $max = 160;
    $('#sms').keyup(function (e) {
        $('#ecran').text("nombre de caractères restant:" + ($max - $('#sms').val().length));
        if (($max - $('#sms').val().length) < 0) {
            $('#ecran').addClass('text-danger');
        } else {
            $('#ecran').removeClass('text-danger');
        }
    });

</script>