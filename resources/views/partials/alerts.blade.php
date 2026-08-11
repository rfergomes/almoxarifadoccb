<script>
  document.addEventListener('DOMContentLoaded', function() {
    toastr.options = {
      closeButton: true,
      progressBar: true,
      positionClass: "toast-top-right",
      showDuration: "300",
      hideDuration: "1000",
      timeOut: "5000",
    };

    @if(session('success'))
      toastr.success(@json(session('success')), 'Sucesso!');
    @endif

    @if(session('error'))
      toastr.error(@json(session('error')), 'Erro!');
    @endif

    @if(session('warning'))
      toastr.warning(@json(session('warning')), 'Atenção!');
    @endif

    @if(session('info'))
      toastr.info(@json(session('info')), 'Informação');
    @endif

    @if($errors->any())
      @foreach($errors->all() as $error)
        toastr.error(@json($error), 'Erro de Validação');
      @endforeach
    @endif
  });
</script>
