
window.fishBatchComponent = () => {
  return {
    fishBatch: null,
    //Listados
    fishpond: null,
    observations: [],
    expenses: [],
    biometries: [],
    deaths: [],
    /** 
     * Controla que listado es el que se muestra en pantalla y 
     * tambien controla que formulario se habilita. [observations, expenses, biometries, deaths]
     */
    tab: 'info',
    // *================================================*
    // *============ PROPIEDADES DE LA VISTA ===========*
    // *================================================*

    initialPopulationWarning: false,
    populationWarning: false,

    /** Se encarga de las peticones al servidor */
    wire: null,
    /** Se encarga de administrar los eventos personalizados */
    dispatch: null,
    /** Permite acceder a las referencias del componente en el DOM */
    refs: null,
    // *===============================================*
    // *============ Metodos del Componente ===========*
    // *===============================================*
    /**
     * Se encarga de montar los datos iniciales
     * @param {*} wire Objeto de livewire encargado de las peticiones
     * @param {*} dispatch Onjeto de alpine encargado de los eventos
     * @param {*} refs Objeto de alpine encargado de las referencias
     */
    init(wire = null, dispatch = null, refs = null) {
      this.wire = wire;
      this.dispatch = dispatch;
      this.refs = refs;
    },
    mountFishBatch(fishBatch) {
      this.tab = 'info';
      this.fishBatch = fishBatch;
      this.fishpond = fishBatch.fishpond;
      this.observations = fishBatch.observations;
      this.expenses = fishBatch.expenses;
      this.deaths = fishBatch.deathReports;
      this.biometries = fishBatch.biometries;

      //Se establece la alerta de la población inicial
      this.initialPopulationWarning = fishBatch.fishpond.capacity
        ? fishBatch.initialPopulation > fishBatch.fishpond.capacity
        : false;

      //Se establece la alerta de la población
      this.populationWarning = fishBatch.fishpond.capacity
        ? fishBatch.population > fishBatch.fishpond.capacity
        : false;
    },
    /**
     * Se encarga de emiter el evento que habilita la aparicion del formulario
     * en pantalla y este en funcíon de la pestaña que se está visualizando.
     * @param {*} data Información adicional que se pasa al formulario
     */
    enableForm(data = null) {
      let info = {
        formName: null,
        fishBatch: this.fishBatch,
        data: data
      }

      if (this.tab === 'observations') {
        info.formName = 'new-fish-batch-observation';
      } else if (this.tab === 'expenses') {
        info.formName = 'new-fish-batch-expense';
      } else if (this.tab === 'deaths') {
        info.formName = 'new-fish-batch-death';
      } else if (this.tab === 'biometries') {
        info.formName = 'new-fish-batch-biometry';
      }

      this.dispatch('enable-form', info);
    },
    updateObservation(observation) {
      let formName = 'update-fish-batch-observation';
      let fishBatch = this.fishBatch;
      let data = observation;

      this.dispatch('enable-form', { formName, fishBatch, data });
    },
    destroyObservation(observation) {
      window.Swal.fire({
        title: "¿Desea eliminar esta observación?",
        text: "Esta acción no puede revertirse.",
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: 'var(--primary)',
        confirmButtonColor: 'var(--success)',
        confirmButtonText: '¡Eliminar!',
        showLoaderOnConfirm: true,
        preConfirm: () => {
          return this.wire.destroyObservation(observation.id).then(res => res);
        },
        allowOutsideClick: () => !window.Swal.isLoadig()
      }).then(result => {
        if (result.isConfirmed) {
          if (result.value.ok || result.value.errors.notFound) {
            //Recupero el index de la observacion
            let index = this.fishBatch.observations.findIndex(item => item.id === observation.id);
            //Se elimina el estanque del arreglo
            if (index >= 0) {
              this.fishBatch.observations.splice(index, 1);
              this.observations = this.fishBatch.observations;
            }
          }
        }
      });
    },
    updateExpense(expense) {
      let formName = 'update-fish-batch-expense';
      let fishBatch = this.fishBatch;
      let data = expense;

      this.dispatch('enable-form', { formName, fishBatch, data });
    },
    destroyExpense(expense) {
      window.Swal.fire({
        title: "¿Desea eliminar este gasto?",
        text: "Esta acción no puede revertirse.",
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: 'var(--primary)',
        confirmButtonColor: 'var(--success)',
        confirmButtonText: '¡Eliminar!',
        showLoaderOnConfirm: true,
        preConfirm: () => {
          return this.wire.destroyExpense(expense.id).then(res => res);
        },
        allowOutsideClick: () => !window.Swal.isLoadig()
      }).then(result => {
        if (result.isConfirmed) {
          if (result.value.ok || result.value.errors.notFound) {
            //Recupero el index de la observacion
            let fishBatch = this.fishBatch;
            this.dispatch('expense-was-deleted', { fishBatch, expense });
          }
        }
      });
    },
    updateDeathReport(report) {
      let formName = 'update-fish-batch-death';
      let fishBatch = this.fishBatch;
      let data = report;
      this.dispatch('enable-form', { formName, fishBatch, data });
    },
    destroyDeathReport(report) {
      window.Swal.fire({
        title: "¿Desea eliminar este reporte?",
        text: "Esta acción no puede revertirse.",
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: 'var(--primary)',
        confirmButtonColor: 'var(--success)',
        confirmButtonText: '¡Eliminar!',
        showLoaderOnConfirm: true,
        preConfirm: () => {
          return this.wire.destroyDeathReport(report.id).then(res => res);
        },
        allowOutsideClick: () => !window.Swal.isLoadig()
      }).then(result => {
        if (result.isConfirmed) {
          if (result.value.ok || result.value.errors.notFound) {
            //Recupero el index de la observacion
            let fishBatch = this.fishBatch;
            this.dispatch('death-was-deleted', { fishBatch, report });
          } else {
            console.log(res.value.errors);
          }
        }
      });
    },
    updateBiometry(biometry) {
      let formName = 'update-fish-batch-biometry';
      let fishBatch = this.fishBatch;
      let data = biometry;

      this.dispatch('enable-form', { formName, fishBatch, data });
    },
    destroyBiometry(biometry) {
      window.Swal.fire({
        title: "¿Desea eliminar este reporte?",
        text: "Esta acción no puede revertirse.",
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: 'var(--primary)',
        confirmButtonColor: 'var(--success)',
        confirmButtonText: '¡Eliminar!',
        showLoaderOnConfirm: true,
        preConfirm: () => {
          return this.wire.destroyBiometry(biometry.id).then(res => res);
        },
        allowOutsideClick: () => !window.Swal.isLoadig()
      }).then(result => {
        if (result.isConfirmed) {
          if (result.value.ok || result.value.errors.notFound) {
            //Recupero el index de la observacion
            let fishBatch = this.fishBatch;
            this.dispatch('biometry-was-deleted', { fishBatch, biometry });
          } else {
            console.log(res.value.errors);
          }
        }
      });
    },
    refresh() {
      this.observations = [];
      this.fishBatch.observations.forEach(item => {
        this.observations.push(item);
      });

      this.expenses = [];
      this.fishBatch.expenses.forEach(item => {
        this.expenses.push(item);
      })

      this.deaths = [];
      this.fishBatch.deathReports.forEach(report => {
        this.deaths.push(report);
      })

      this.biometries = [];
      this.fishBatch.biometries.forEach(item => {
        this.biometries.push(item);
      })
    }
  }
}