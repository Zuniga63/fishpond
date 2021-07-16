const _ = require("lodash");

const dayjs = require("dayjs");
require('dayjs/locale/es-do');

//Se adiciona el pluging para tiempo relativo
let relativeTime = require('dayjs/plugin/relativeTime');
dayjs.extend(relativeTime);

let isSameOrBefore = require('dayjs/plugin/isSameOrBefore');
dayjs.extend(isSameOrBefore);

//Se establece en español
dayjs.locale('es-do');


window.app = () => {
  return {
    tab: 'sown-lot',    //[sown-lot, harvested-batch]
    /** Define si la vista es del home o de algun lote */
    home: true,
    /** Arreglo con todos los lotes de peces */
    allFishBatchs: [],
    /** Arreglo con los estanques que están almacenados */
    fishponds: [],
    /** Arreglo con los lotes de peces correspodientes al tab */
    fishBatchs: [],
    /** Respondable de mostrar los formularios en pantalla */
    formActive: false,
    /** Se encarga de las peticones al servidor */
    wire: null,
    /** Se encarga de administrar los eventos personalizados */
    dispatch: null,
    /** Permite acceder a las referencias del componente en el DOM */
    refs: null,
    // *===========================================================*
    // *================= METODOS DEL COMPONENTE ==================*
    // *===========================================================*
    init(wire = null, dispatch = null) {
      this.wire = wire;
      this.dispatch = dispatch;
      let data = window.initialData;

      //Se crean los estanques
      data.fishponds.forEach(record => {
        let fishpond = this.createFishpond(record);
        this.fishponds.push(fishpond);
      });

      //Se crean los lotes de peces
      data.fishBatchs.forEach(record => {
        let fishBatch = this.createFishBatch(record);
        this.allFishBatchs.push(fishBatch);
      });

      //Se seleccionan los lotes que están sembrados
      this.updateFishBatchList();
      setTimeout(() => {
        this.selectFishBatch(this.fishBatchs[0]);
      }, 1000);
    },
    /**
     * Recibe los datos puros del servidor y los convierte en instancias de 
     * lotes. Este metodo se debe llamar despues de haber creado los estanqes
     * @param {*} data Datos puros de lotes
     */
    createFishBatch(data) {
      //Se crea el objeto con los datos basicos
      let fishBatch = {
        id: data.id,
        seedtime: dayjs(data.seedtime),
        harvest: data.harvest ? dayjs(data.harvest) : null,
        initialPopulation: data.initialPopulation,
        population: data.population,
        initialWeight: data.initialWeight,
        amount: data.amount,
        observations: [],
        createdAt: dayjs(data.createdAt),
        updatedAt: dayjs(data.updatedAt),
      }

      //Se crean las observaciones
      data.observations.forEach(item => {
        fishBatch.observations.push(this.createObservation(item));
      });


      //Se recupea el estanque
      let fishpond = this.fishponds.find(item => item.id === data.fishpondId);
      //Se siembran los peces
      fishpond.seed(data.population);
      //Se agrega al objeto
      fishBatch.fishpond = fishpond;

      //Se calcula la eda del lote
      fishBatch.age = fishBatch.harvest
        ? fishBatch.harvest.from(fishBatch.seedtime, true)
        : fishBatch.seedtime.fromNow(true);

      //Se calcula la biomasa
      let biomass = data.population * data.initialWeight;
      let biomassUnit = 'g.';

      if (biomass >= 1000) {
        biomass = biomass / 1000;
        biomassUnit = 'Kg.'
      }

      fishBatch.biomass = {
        value: _.round(biomass, 2),
        unit: biomassUnit
      };

      //Se calculan las muer
      console.log(fishBatch);
      return fishBatch;

    },
    /**
     * Recibe los datos puros del servidor y los convierte en una instancia
     * de estanque para que pueda ser usado con un lote.
     * @param {*} data Objeto con los datos generales de un estanque
     */
    createFishpond(data) {
      let fishpond = {};
      const FISHPOND_TYPES = {
        rectangular: 'Rectangular',
        circular: 'Circular',
      }

      //Se guarda la información basica
      fishpond.id = data.id;
      fishpond.name = data.name;
      fishpond.type = FISHPOND_TYPES[data.type];
      fishpond.capacity = data.capacity;
      fishpond.inUse = data.inUse;

      //Se calcula el area en m2
      if (data.type === 'rectangular') {
        fishpond.area = data.width && data.long
          ? _.round(data.width * data.long, 2)
          : null;
      } else if (data.type === 'circular') {
        fishpond.area = data.diameter
          ? _.round(Math.PI * Math.pow((data.diameter / 2.0), 1))
          : null;
      }

      //Se calcula el volumen en m3
      if (fishpond.area && data.effectiveHeight || data.maxHeight) {
        let height = data.effectiveHeight || data.maxHeight;
        fishpond.volume = _.round(fishpond.area * height, 1);
      }

      //Se agregan las variable situacionales
      fishpond.population = null;
      fishpond.densityByArea = null;
      fishpond.desityByVolume = null;

      //Se agrea un metodo para sembrar una poblacion
      //que se encarga de actualizar las variables situacionales
      fishpond.seed = function (population = null) {
        if (population && population > 0) {
          this.population = population;
          this.densityByArea = this.area ? _.round(population / this.area, 0) : null;
          this.densityByVolume = this.volume ? _.round(population / this.volume, 0) : null;
        }
      }

      return fishpond;
    },
    createObservation(data) {
      return {
        id: data.id,
        title: data.title,
        message: data.message,
        createdAt: dayjs(data.createdAt),
        updatedAt: dayjs(data.updatedAt),
        createIsSameUpdate: dayjs(data.createdAt).isSame(dayjs(data.updatedAt)),
      };
    },
    /**
     * Cambia el valor de la varible tab y actualiza el listado de
     * lotes que se muestran en el panel principal.
     * @param {string} tab En nombre del tab por el que se va a cambiar
     */
    changeTab(tab = 'sown-lot') {
      if (tab !== this.tab) {
        this.tab = tab;
        this.updateFishBatchList();
      }
      //Metodo para actualizar el listado de lotes
    },
    updateFishBatchList() {
      let tab = this.tab;
      //Se recuperan los lotes segun el tab
      if (tab === 'sown-lot') {
        this.fishBatchs = this.allFishBatchs.filter(b => b.harvest === null);
      } else if (tab === 'harvested-batch') {
        this.fishBatchs = this.allFishBatchs.filter(b => b.harvest !== null);
      }

      //Algoritmo para ordenar por la fecha de siembra
    },
    enableForm(name = null, fishBatch = null, data = null) {
      if (name) {
        this.formActive = true;
        data = {
          mode: 'register',
          fishBatch: fishBatch,
          data: data,
        }

        if (name === 'new-fish-batch') {
          this.dispatch('enable-fish-batch-form', data);
        } else if (name === 'update-fish-batch') {
          data.mode = "updating";
          this.dispatch('enable-fish-batch-form', data);
        } else if (name === 'new-fish-batch-observation') {
          this.dispatch('enable-fish-batch-observation-form', data);
        } else if (name === 'update-fish-batch-observation') {
          data.mode = 'updating';
          this.dispatch('enable-fish-batch-observation-form', data);
        } else {
          this.formActive = false;
        }
      }
    },
    /**
     * Se encarga de crear las instancias de los lotes y agregarlos en el arreglo principal
     * @param {*} data Objetos con los datos de un lote de peces
     */
    addNewFishBatch(data) {
      console.log(data);
      //Se crea la instancia
      let fishBatch = this.createFishBatch(data);
      //Se agrega al arreglo principal
      this.allFishBatchs.push(fishBatch);
      //Se actualiza el listado
      this.updateFishBatchList();
      this.formActive = false;
    },
    updateFishBatch(data) {
      //Se crea una nueva instancia
      let fishBatchUpdated = this.createFishBatch(data);
      //Se recupera la antigua instnacia
      let lastFishBatch = this.allFishBatchs.find(item => item.id === fishBatchUpdated.id);
      //Se actualizan todos los campos
      for (const key in fishBatchUpdated) {
        if (Object.hasOwnProperty.call(fishBatchUpdated, key)) {
          lastFishBatch[key] = fishBatchUpdated[key];
        }
      }

      //Se actualiza el listado
      this.updateFishBatchList();
      this.formActive = false;
    },
    addObservation(detail) {
      //Recupero el lote de peces
      let fishBatch = this.allFishBatchs.find(batch => batch.id === detail.fishBatch.id);
      //Se crea la observación
      let observation = this.createObservation(detail.observation);
      //Se agrega al listado
      fishBatch.observations.push(observation);
      this.dispatch('observation-was-added');
      //Se deshabilita el formulario
      this.formActive = false;
    },
    updateObservation(detail){
      //Recupero el lote de peces
      let fishBatch = this.allFishBatchs.find(batch => batch.id === detail.fishBatch.id);
      //Se crea la observación
      let observation = this.createObservation(detail.observation);
      //Se busca la observación original
      let original = fishBatch.observations.find(item => item.id === observation.id);
      //Se actualizan los campos
      for (const key in observation) {
        if (Object.hasOwnProperty.call(original, key)) {
          original[key] = observation[key];          
        }
      }

      //Se deshabilita el formulario
      this.formActive = false;
    },
    __printSubmitData(data) {
      let bodyLength = 60;
      let separator = '-';
      let header = '';
      let left = `+${separator}`;
      let right = `${separator}+`;
      let text = '';

      header = left + separator.repeat(bodyLength) + right + '\n';
      text = header;

      for (const key in data) {
        if (Object.hasOwnProperty.call(data, key)) {
          let value = data[key] ? data[key].toString() : 'null';
          let keyLength = key.length;
          let valueLength = value.length;
          let line = `${key}: ${value}`;
          if (line.length <= bodyLength) {
            line += ' '.repeat(bodyLength - line.length);
            text += `| ${line} |\n`;
          } else {
            // let first = line.slice(0, bodyLength - 1);
            // let last = line.slice(bodyLength, 259);

            // text += `| ${first} |\n`;
            // text += '| ' + ' '.repeat(keyLength + 2);
            // text += '| ' + " ".repeat(bodyLength - last.length) + ' |' + '\n'
          }
        }//end if
      }//end for
      text += header;
      console.log(text, data);
    },
    selectFishBatch(fishBatch) {
      this.home = false;
      this.dispatch('fish-batch-selected', fishBatch);
    }
  }
}

require('./fish-batch-form');
require('./fish-batch-component');
require('./fish-batch-observation-form');