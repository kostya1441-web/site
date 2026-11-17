using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace Rabota3
{
    public partial class Form2 : Form
    {
        public Form2()
        {
            InitializeComponent();
        }

        private void Form2_FormClosing(object sender, FormClosingEventArgs e)
        {
            if (DialogResult != DialogResult.OK)
                return;

            if (Nazv_TB.Text.Trim() == "")
            {
                MessageBox.Show("Вы не ввели название блюда", "Ошибка");
                Nazv_TB.Focus();
                e.Cancel = true;
                return;
            }

            if (Ves_TB.Text.Trim() == "")
            {
                MessageBox.Show("Вы не ввели вес блюда", "Ошибка");
                Ves_TB.Focus();
                e.Cancel = true;
                return;
            }
            double Ves;
            if (!double.TryParse(Ves_TB.Text, out Ves))
            {
                MessageBox.Show("Недопустимые символы в строке веса", "Ошибка");
                Ves_TB.Focus();
                e.Cancel = true;
                return;
            }
            if (Ves < 0.1 || Ves > 3.0)
            {
                MessageBox.Show("Вес должен быть в диапазоне от 0,1 до 3,0", "Ошибка");
                Ves_TB.Focus();
                e.Cancel = true;
                return;
            }

            if (Seb_TB.Text.Trim() == "")
            {
                MessageBox.Show("Вы не ввели себестоимость блюда", "Ошибка");
                Seb_TB.Focus();
                e.Cancel = true;
                return;
            }
            int Seb;
            if (!int.TryParse(Seb_TB.Text, out Seb))
            {
                MessageBox.Show("Недопустимые символы в строке себестоимости", "Ошибка");
                Seb_TB.Focus();
                e.Cancel = true;
                return;
            }
            if (Seb < 1 || Seb > 5000)
            {
                MessageBox.Show("Себестоимость должна быть в диапазоне от 1 до 5000", "Ошибка");
                Seb_TB.Focus();
                e.Cancel = true;
                return;
            }

            if (Cena_TB.Text.Trim() == "")
            {
                MessageBox.Show("Вы не ввели цену блюда", "Ошибка");
                Cena_TB.Focus();
                e.Cancel = true;
                return;
            }
            int Cena;
            if (!int.TryParse(Cena_TB.Text, out Cena))
            {
                MessageBox.Show("Недопустимые символы в строке цены", "Ошибка");
                Cena_TB.Focus();
                e.Cancel = true;
                return;
            }
            if (Cena < 1 || Cena > 10000)
            {
                MessageBox.Show("Цена должна быть в диапазоне от 1 до 10000", "Ошибка");
                Cena_TB.Focus();
                e.Cancel = true;
                return;
            }
            if (Cena <= Seb)
            {
                MessageBox.Show("Цена должна быть больше себестоимости", "Ошибка");
                Cena_TB.Focus();
                e.Cancel = true;
                return;
            }
        }
    }
}
