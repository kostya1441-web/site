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

            if (Surname_TB.Text.Trim() == "")
            {
                MessageBox.Show("Вы не ввели фамилию", "Ошибка");
                Surname_TB.Focus();
                e.Cancel = true;
                return;
            }

            if (Year_TB.Text.Trim() == "")
            {
                MessageBox.Show("Вы не ввели год рождения", "Ошибка");
                Year_TB.Focus();
                e.Cancel = true;
                return;
            }
            int Year;
            if (!int.TryParse(Year_TB.Text, out Year))
            {
                MessageBox.Show("Недопустимые символы в строке года рождения", "Ошибка");
                Year_TB.Focus();
                e.Cancel = true;
                return;
            }
            if (Year < 1900 || Year > DateTime.Now.Year)
            {
                MessageBox.Show("Год рождения должен быть в диапазоне от 1900 до " + DateTime.Now.Year, "Ошибка");
                Year_TB.Focus();
                e.Cancel = true;
                return;
            }

            if (!Male_RB.Checked && !Female_RB.Checked)
            {
                MessageBox.Show("Вы не выбрали пол", "Ошибка");
                Male_RB.Focus();
                e.Cancel = true;
                return;
            }

            if (Height_TB.Text.Trim() == "")
            {
                MessageBox.Show("Вы не ввели рост", "Ошибка");
                Height_TB.Focus();
                e.Cancel = true;
                return;
            }
            int Height;
            if (!int.TryParse(Height_TB.Text, out Height))
            {
                MessageBox.Show("Недопустимые символы в строке роста", "Ошибка");
                Height_TB.Focus();
                e.Cancel = true;
                return;
            }
            if (Height < 30 || Height > 250)
            {
                MessageBox.Show("Рост должен быть в диапазоне от 30 до 250", "Ошибка");
                Height_TB.Focus();
                e.Cancel = true;
                return;
            }
        }
    }
}
