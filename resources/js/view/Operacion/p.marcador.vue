<template>
    <div class="row">
        <div class="col-sm-6">
             <div class="card">
                 <div class="card-header">
                    <h4 class="card-title">Registro de Marcación</h4>
                </div>
                <div class="card-body">
                    <!-- <h4 class="text-center">TURNO {{ turno }}</h4> -->
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <digital-clock :blink="true"/>
                        </div>
                        <div class="col-12">
                            <form v-on:submit.prevent="guardar()">
                                <Input title="Codigo de Barras" type="number" :focusSelect="true" v-model="codigo_barras"></Input>
                                <button type="submit" hidden></button>
                            </form>
                        </div>
                    </div>
                </div>
             </div>
        </div>
        <div class="col-sm-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Respuesta</h4>
                </div>
                <div class="card-body">
                    <div v-if="alert!=null" :class="'alert alert-'+alert.status" role="alert">
                        {{ alert.data }}
                    </div>
                    <div class="text-center">
                        <img v-if="foto!=null" style="max-width: 40%;height: auto;" :src="url(foto)" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapState,mapMutations } from 'vuex'

import DigitalClock from "vue-digital-clock";
import Input from '../../dragon-desing/dg-input.vue'
export default {
    components:{
        Input,
        DigitalClock
    },
    data() {
        return {
            turnos:[],
            codigo_barras: null,
            respuesta: null,
            alert: null,
            foto: null,
        }
    },
    computed: {
        ...mapState(['turno','fundo','conexion']),
    },
    mounted() {
        db.transaction((tx)=>{
            // tx.executeSql('DROP TABLE MARCAS');
            tx.executeSql('CREATE TABLE IF NOT EXISTS MARCAS(codigo,fecha,fundo)');
        });
        this.listarTurnos();
    },
    methods: {
        url(foto){
            return url_base+'/../storage/operador/'+foto;
        },
        listarTurnos(){
            axios.get(url_base+'/turno?all=true')
            .then(response => {
                this.turnos = response.data;
                if (this.turnos.length>0) {
                    this.turno_id=this.turnos[0].id;
                }
            });
        },
        guardar(){
            // this.clearAlert();
            this.$nextTick(() =>{
                this.foto=null;
                if (this.codigo_barras.length==8) {
                    var cod_barras_paso=this.codigo_barras;
                    this.codigo_barras=null;
                    
                    /**
                     * Con conexion
                     */
                    if (this.conexion) {
                        axios.post(url_base+'/marcador2',{ codigo_barras: cod_barras_paso,turno: this.turno,fundo: this.fundo})
                        .then(response => {
                            var resp=response.data;
                            switch (resp.status) {
                                case "VALIDATION":
                                    this.errors_editar=resp.data;
                                    break;
                                case "OK":
                                    this.alert={
                                        status: 'primary',
                                        data: resp.data
                                    }
                                    this.foto=resp.foto;
                                    break;
                                case "ERROR":
                                    this.alert={
                                        status: 'warning',
                                        data: resp.data
                                    }
                                    break;
                            }
                        })
                    }
                    /**
                     * Sin conexion
                     */
                    else{
                        db.transaction((tx)=>{
                            tx.executeSql('INSERT INTO MARCAS(codigo,fecha,fundo) VALUES (?,?,?)',
                                [cod_barras_paso,moment().format('YYYY-MM-DD HH:mm'),this.fundo]
                            ,(res)=>{
                                this.alert={
                                    status: 'primary',
                                    data: "Marca Correcta (offline)."
                                }
                            },(tx, err) => {
                                console.log(err);
                                
                                alert("Error processing SQL: "+err);
                            });
                        });
                    }

                }else{
                    if (this.codigo_barras=="1001") {
                        this.$store.commit( 'update_turno' , 1 );
                        this.alert={
                            status: 'success',
                            data: 'Turno 01 Actualizado'
                        }
                    }else if(this.codigo_barras=="1002"){
                        this.$store.commit( 'update_turno' , 2 );
                        this.alert={
                            status: 'success',
                            data: 'Turno 02 Actualizado'
                        }
                    }else{
                        this.alert={
                            status: 'danger',
                            data: 'Código no Valido'
                        }
                    }
                    this.codigo_barras=null;
                }
            })
        },
        clearAlert(){
            setTimeout(() => {
                this.alert=null;
            }, 10000);
        }
    },
}
</script>