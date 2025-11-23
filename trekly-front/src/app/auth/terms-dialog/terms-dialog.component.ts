import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatDialogModule } from '@angular/material/dialog';
import { MatButtonModule } from '@angular/material/button';

@Component({
    selector: 'app-terms-dialog',
    standalone: true,
    imports: [CommonModule, MatDialogModule, MatButtonModule],
    template: `
        <h2 mat-dialog-title>Declaración de Responsabilidad y Políticas de Uso – Plataforma RUNPAL</h2>
        <mat-dialog-content class="terms-content">
            <p><strong>Este documento establece las políticas, declaraciones de responsabilidad y condiciones de uso que debe aceptar toda persona mayor de edad que se registre en la plataforma RUNPAL</strong>, una solución web diseñada para facilitar la creación, organización y participación en actividades recreativas, deportivas, sociales y eventos entre usuarios.</p>
            
            <p>Al registrarse y hacer uso de RUNPAL, el usuario declara y acepta lo siguiente:</p>

            <h3>1. RESPONSABILIDAD PERSONAL</h3>
            <ul>
                <li>El usuario reconoce que cualquier actividad física, deportiva o social puede implicar riesgos, tales como lesiones, accidentes, daños materiales, traumas u otros sucesos no deseados.</li>
                <li>Toda participación en eventos creados dentro de RUNPAL es voluntaria y bajo la exclusiva responsabilidad del usuario.</li>
                <li>RUNPAL no es responsable por lesiones, accidentes, daños físicos, consecuencias médicas, incapacidad, traumas, o muerte que pueda sufrir cualquier usuario durante actividades organizadas por terceros dentro de la plataforma.</li>
                <li>El usuario afirma que goza de buena salud, se encuentra en condiciones físicas aptas para participar y actúa bajo decisión personal libre e informada.</li>
            </ul>

            <h3>2. PAGOS Y TRANSACCIONES</h3>
            <ul>
                <li>RUNPAL no se responsabiliza por pagos realizados entre usuarios, transferencias externas, acuerdos económicos o compras de productos o servicios no administrados directamente por RUNPAL.</li>
                <li>Cualquier pérdida económica, fraude, estafa, cobro indebido o conflicto monetario entre usuarios es responsabilidad exclusiva de las partes involucradas.</li>
                <li>RUNPAL no actúa como garante, asegurador, intermediario financiero ni auditor de transacciones realizadas entre usuarios.</li>
            </ul>

            <h3>3. ACTIVIDADES Y EVENTOS CREADOS POR USUARIOS</h3>
            <ul>
                <li>RUNPAL no verifica ni certifica la idoneidad, experiencia, seguridad, confiabilidad o legalidad de los eventos creados por los usuarios.</li>
                <li>El usuario creador es el único responsable de la logística, seguridad, descripción, veracidad de la información, y riesgos asociados a cualquier evento que publique.</li>
                <li>RUNPAL no asume responsabilidad legal por actos, omisiones, negligencia u omisión de seguridad por parte de organizadores o participantes.</li>
            </ul>

            <h3>4. RIESGOS Y SEGUROS PERSONALES</h3>
            <ul>
                <li>Todo usuario declara que cuenta con los seguros personales necesarios (salud, vida, accidentes, deportivos, etc.) antes de participar en cualquier actividad publicada en RUNPAL.</li>
                <li>RUNPAL no provee ningún tipo de seguro médico, deportivo, de accidentes o responsabilidad civil.</li>
                <li>El usuario acepta que cualquier gasto médico o legal derivado de su participación en actividades es de su absoluta responsabilidad.</li>
            </ul>

            <h3>5. COMPORTAMIENTO Y CONDUCTA</h3>
            <ul>
                <li>Está prohibido publicar eventos peligrosos, ilegales, discriminatorios o que pongan en riesgo a otras personas.</li>
                <li>RUNPAL puede suspender o eliminar usuarios o eventos que incumplan estas políticas.</li>
                <li>Cualquier acto de violencia, agresión, hurto, acoso o conducta inapropiada entre usuarios es responsabilidad directa del infractor.</li>
            </ul>

            <h3>6. USO DE LA INFORMACIÓN</h3>
            <ul>
                <li>El usuario acepta que RUNPAL puede usar la información necesaria para el funcionamiento de la plataforma según las políticas de privacidad vigentes.</li>
                <li>RUNPAL no comparte información personal con terceros salvo obligación legal.</li>
            </ul>

            <h3>7. ACEPTACIÓN DE RIESGO</h3>
            <p>El usuario declara expresamente:</p>
            <ul>
                <li>Que entiende los riesgos asociados a las actividades de RUNPAL.</li>
                <li>Que participa bajo su propio riesgo y responsabilidad.</li>
                <li>Que libera a RUNPAL, sus administradores, desarrolladores y aliados de cualquier reclamo, demanda o acción legal derivada de su participación o interacción con otros usuarios.</li>
            </ul>

            <h3>8. ACEPTACIÓN TOTAL DE ESTAS POLÍTICAS</h3>
            <ul>
                <li>Al registrarse, el usuario confirma que ha leído, comprendido y aceptado en su totalidad este documento.</li>
                <li>La aceptación es un requisito obligatorio para crear cuenta en RUNPAL y usar sus servicios.</li>
            </ul>

            <p style="margin-top: 20px;"><strong>Este documento debe ser aceptado de forma expresa para proceder con el registro en la plataforma RUNPAL.</strong></p>
        </mat-dialog-content>
        <mat-dialog-actions align="end">
            <button mat-button mat-dialog-close color="primary">Cerrar</button>
        </mat-dialog-actions>
    `,
    styles: [`
        .terms-content {
            max-height: 60vh;
            overflow-y: auto;
            padding: 20px;
            line-height: 1.6;
        }
        
        h3 {
            color: #3f51b5;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        
        ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        
        li {
            margin: 8px 0;
        }
        
        p {
            margin: 10px 0;
        }
    `]
})
export class TermsDialogComponent { }
